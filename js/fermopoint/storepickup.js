var FermopointStorePickup = Class.create();
FermopointStorePickup.prototype = {

    initialize: function(changeMethodUrl, searchUrl, mediaUrl, infoCallback) {
        this.changeMethodUrl = changeMethodUrl;
        this.searchUrl = searchUrl;
        this.mediaUrl = mediaUrl;
        this.infoCallback = infoCallback;
        
        this.error = false;
		this.points = [];
        this.location = null;
        this.map = null;
        this.clusterer = null;
        this.infoWindow = null;
        
        this.onInit = null;
        this.onSearchStart = null;
        this.onSearchEnd = null;
        this.onSelectPoint = null;
        
        this.setUpHook();
    },
    
    forceMapUpdate: function () {
        google.maps.event.trigger(this.map, 'resize');
    },
    
    clearValidations: function (input) {
    },
    
    onAccountTypeChange: function (event, target) {
        if (this.nickname.value.length)
            Validation.validate(this.nickname);
        if (this.dob.value.length)
            Validation.validate(this.dob);
    },
    
    onNicknameChange: function (event, target) {
        Validation.validate(this.nickname);
        if (this.dob.value.length)
            Validation.validate(this.dob);
    },
    
    onDobChange: function (event, target) {
        Validation.validate(this.dob);
    },
    
    validateNickname: function (v) {
        var result,
            self = this;
        if ( ! v.length || ! this.newAccount.checked)
            return true;
        
        result = true;
        this.nickname.up('.input-box').addClassName('loading');
        new Ajax.Request(this.nicknameUrl, {
            method: 'post',
            parameters: {
                nickname: v
            },
            asynchronous: false,
            onSuccess: function (transport) {
                var response = transport.responseText;
                
                result = response === 'ok';
            },
            onComplete: function() {
                self.nickname.up('.input-box').removeClassName('loading');
            }
        });
        
        return result;
    },
    
    validateDob: function (v) {
        var result,
            self = this;
        if ( ! v.length || ! this.existingAccount.checked || ! this.nickname.value.length)
            return true;
        
        result = true;
        this.dob.up('.input-box').addClassName('loading');
        new Ajax.Request(this.dobUrl, {
            method: 'post',
            parameters: {
                nickname: this.nickname.value,
                dob: v
            },
            asynchronous: false,
            onSuccess: function (transport) {
                var response = transport.responseText;
                
                result = response === 'ok';
            },
            onComplete: function() {
                self.dob.up('.input-box').removeClassName('loading');
            }
        });
        
        return result;
    },
    
    bindValidations: function (newAccount, existingAccount, guestAccount, nickname, dob, nicknameUrl, dobUrl) {
        this.newAccount = newAccount;
        this.existingAccount = existingAccount;
        this.guestAccount = guestAccount;
        this.nickname = nickname;
        this.dob = dob;
        this.nicknameUrl = nicknameUrl;
        this.dobUrl = dobUrl;
        
        if (newAccount)
            newAccount.observe('change', this.onAccountTypeChange.bind(this));
        if (existingAccount)
            existingAccount.observe('change', this.onAccountTypeChange.bind(this));
        if (guestAccount)
            guestAccount.observe('change', this.onAccountTypeChange.bind(this));
        
        Validation.add('validate-fp-nickname', 'User with this nickname already exists', this.validateNickname.bind(this));  
        Validation.add('validate-fp-dob', 'There is no user with given nickname and date of birth', this.validateDob.bind(this));  
 
        if (nickname)
            nickname.addClassName('validate-fp-nickname').observe('change', this.onNicknameChange.bind(this));
        if (dob)
            dob.addClassName('validate-fp-dob').observe('change', this.onDobChange.bind(this));
    },
    
    setUpHook: function () {
        var fallbackValidate = ShippingMethod.prototype.validate,
            fallbackNextStep = ShippingMethod.prototype.nextStep;
        ShippingMethod.prototype.validate = function () {
            var result,
                pointId,
                methods;
                
            result = fallbackValidate.call(this);
            if ( ! result)
                return false;
                
            methods = document.getElementsByName('shipping_method');
            for (var i=0; i<methods.length; i++) {
                if (methods[i].checked && methods[i].value !== 'fpstorepickup_fpstorepickup') {
                    return true;
                }
            }
            
            /*if ($('fermopoint_accept_terms') && $('fermopoint_accept_terms').visible() && ! $('fermopoint_accept_terms').checked) {
                alert(Translator.translate('You should accept FermoPoint terms and conditions').stripTags());
                return false;
            }*/
            pointId = parseInt($('fermopoint_point_id').value, 10);
            if (pointId <= 0) {
                alert(Translator.translate('You should select one of available pick-up points to continue').stripTags());
                return false;
            }
            
            return true;
        };
        
        ShippingMethod.prototype.nextStep = function (transport) {
            fallbackNextStep.call(this, transport);
            checkout.reloadProgressBlock('shipping');
        };
    },
    
    rebound: function () {
        var i,
            bounds;
            
        if ( ! this.points.length)
            return;
            
        bounds = new google.maps.LatLngBounds();
            
        if (this.location)
            bounds.extend(this.location.getPosition());
            
        for (i = 0; i < this.points.length; i++)
            bounds.extend(this.points[i].marker.getPosition());
            
        this.map.fitBounds(bounds);
    },
    
    showPointInfo: function (marker, point) {
        var days = [],
            content;
        for (var i = 0; i < point.hours.length; i++) {
            days.push('<span class="dow">' + point.hours[i].day + '</span>' + point.hours[i].hours.join(', '));
        }
        if (this.location) 
            point.distance = Math.abs(this.calcDistance(this.location.getPosition(), marker.getPosition()));
        if (typeof this.infoCallback === 'function')
            content = this.infoCallback(point);
        else
            content = '<div class="fermopoint-info-window">' +
            '<div class="fermopoint-info-row title">' + point.name + '</div>' +
            '<div class="fermopoint-info-row select"><a class="fermopoint-select-me" rel="' + point.id + '" href="#">' + Translator.translate('Select this pick-up point') + '</a></div>' +
            '<div class="fermopoint-info-row"></div>' +
            '<div class="fermopoint-info-row distance"><strong>' + Translator.translate('Distance') + ': </strong>' + point.distance + ' km </div>' +
            '<div class="fermopoint-info-row contact"><strong>' + Translator.translate('Contact') + ': </strong>' + point.contact + '</div>'+
            '<div class="fermopoint-info-row category"><strong>' + Translator.translate('Category') + ': </strong>' + point.category + '</div>'+
            '<div class="fermopoint-info-row hours"><!--strong>' + Translator.translate('Hours') + ': </strong--><div class="hours-list">' + days.join('<br />') + '</div></div></div>'
        ;
        this.infoWindow.setContent(content);
        this.infoWindow.open(this.map, marker);  
    },
    
    calcDistance: function(p1, p2) {
        return (google.maps.geometry.spherical.computeDistanceBetween(p1, p2) / 1000).toFixed(1);
    },
    
    addMarker: function (point) {
        var caller = this,
            marker = new google.maps.Marker({
            position: new google.maps.LatLng(point.latitude, point.longitude),
            map: this.map,
            icon: this.mediaUrl + 'marker_point.png'
        });
        google.maps.event.addListener(marker, 'click', function () {
            caller.showPointInfo(marker, point);
        });
        return marker;
    },
	
	setPoints: function (points) {
        var i, point, markers, marker;
        for (i = 0; i < this.points.length; i++)
            this.points[i].marker.setMap(null);
            
        this.clusterer.clearMarkers();
    
		this.points = points;
        if ( ! this.points.length)
            return;
        
        markers = [];
        for (i = 0; i < this.points.length; i++) {
            point = this.points[i];
            marker = this.addMarker(point);
            this.points[i].marker = marker;
            markers.push(marker);
        }
        
        this.clusterer.addMarkers(markers);
        
        //this.rebound();
	},
    
    choosePoint: function (id) {
        this.infoWindow.close();
        if (typeof(this.onSelectPoint) === 'function')
            for (var i = 0; i < this.points.length; i++)
                if (this.points[i].id == id)
                    this.onSelectPoint.call(this, this.points[i]);
    },
	
    initMap: function (container) {
        var mapOptions = {
                zoom: 5,
                center: new google.maps.LatLng(41.9000, 12.4833)
            },
            caller = this;
        
        Event.observe(container, 'click', function (event) { 
            var target = Event.findElement(event);

            if (target.hasClassName('fermopoint-select-me')) {
                Event.stop(event);
                caller.choosePoint(parseInt(target.rel), 10);
            }
        });
        
        this.infoWindow = new google.maps.InfoWindow();
        this.map = new google.maps.Map(document.getElementById(container), mapOptions);
        this.clusterer = new MarkerClusterer(this.map, this.markers, {
            gridSize: 45,
            styles: [{
                height: 36, width: 36, textColor: "#ffffff", textSize: 11,
                url: this.mediaUrl +  'cluster1.png'
            }, {
                height: 42, width: 42, textColor: "#ffffff", textSize: 11,
                url: this.mediaUrl +  'cluster2.png'
            }, {
                height: 50, width: 50, textColor: "#ffffff", textSize: 11,
                url: this.mediaUrl +  'cluster3.png'
            }, {
                height: 60, width: 60, textColor: "#ffffff", textSize: 11,
                url: this.mediaUrl +  'cluster4.png'
            }]
        });
        
        this.searchBounds(41.9000, 12.4833, 5000);
        
        if (typeof(this.onInit) === 'function')
            this.onInit.call(this);
    },
    
    setLocation: function (lat, lng) {
        var position = new google.maps.LatLng(lat, lng);
        if (this.location)
            this.location.setMap(null);
        this.location = new google.maps.Marker({
            position: position,
            map: this.map
        });
        this.map.setCenter(position);
        this.map.setZoom(12);
        
        //this.rebound();
    },
    
    searchBounds: function (latitude, longitude, radius) {
        var url = this.searchUrl,
            caller = this;
            
        if (typeof(this.onSearchStart) === 'function')
            this.onSearchStart.call(this);
        
        new Ajax.Request(url, {
            parameters: {
                latitude: latitude,
                longitude: longitude,
                radius: radius
            },
            onComplete: function () {
                if (typeof(caller.onSearchEnd) === 'function')
                    caller.onSearchEnd.call(caller, caller.points, caller.error);
            },
            onSuccess: function(transport) {
                var json = transport.responseText.evalJSON();
                caller.error = json.error;
                
                if (json.error)
                    return;
                
                caller.setPoints(json.points);
            }
        });
    },
    
    search: function (address, radius) {
        var url = this.searchUrl,
            caller = this;
            
        if ( ! address.length)
            return;
            
        if (typeof(this.onSearchStart) === 'function')
            this.onSearchStart.call(this);
        
        new Ajax.Request(url, {
            parameters: {
                address: address,
                radius: radius
            },
            onComplete: function () {
                if (typeof(caller.onSearchEnd) === 'function')
                    caller.onSearchEnd.call(caller, caller.points, caller.error);
            },
            onSuccess: function(transport) {
                var json = transport.responseText.evalJSON();
                caller.error = json.error;
                
                if (json.error) {
                    setTimeout(function () {
                        alert(json.message ? json.message : Translator.translate('Error'));
                    }, 1);
                    return;
                }
                
                caller.setLocation(json.latitude, json.longitude);
            }
        });
    },
    
    initGoogleMap: function (scriptUrl, container) {
        if ( ! window.google || ! google.maps)
            this.loadGoogleMap(scriptUrl, container);
        else
            this.initMap(container);
    },
    
    loadGoogleMap: function (scriptUrl, container) {
        var script = document.createElement('script'),
            functionName = 'initMap' + Math.floor(Math.random() * 1000001),
            caller = this;
            
        window[functionName] = function () {
            caller.initMap(container);
        }
        
        script.type = 'text/javascript';
        script.src = scriptUrl + '&callback=' + functionName;
        document.body.appendChild(script);
    },
	
	setUseStorePickup: function(flag, callback)
	{
		var url = this.changeMethodUrl;	
		
		if (flag)
			url += 'flag/1';
		else
			url += 'flag/0';
		
		var request = new Ajax.Request(url, {
            method: 'get', 
            onSuccess: function () {
                try {
                    if (typeof callback === 'function')
                        callback.apply(this, [flag]);
                } catch (e) {
                    
                }
                try {
                    var url = flag ? checkout.urls.billing_address : checkout.urls.shipping_address,
                    sections = FireCheckout.Ajax.getSectionsToUpdate('shipping');

                    if (sections.length) {
                        checkout.update(url, FireCheckout.Ajax.arrayToJson(sections));
                    }
                } catch (e) {
                }
            },
            onFailure: ""
        }); 			
	}
	
}
