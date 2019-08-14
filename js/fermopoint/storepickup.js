var FermopointStorePickup = Class.create();
FermopointStorePickup.prototype = {

    initialize: function(changeMethodUrl, searchUrl, mediaUrl) {
        this.changeMethodUrl = changeMethodUrl;
        this.searchUrl = searchUrl;
        this.mediaUrl = mediaUrl;
        
        this.error = false;
		this.points = [];
        this.markers = [];
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
            
        if ( ! this.markers.length)
            return;
            
        bounds = new google.maps.LatLngBounds();
            
        if (this.location)
            bounds.extend(this.location.getPosition());
            
        for (i = 0; i < this.markers.length; i++)
            bounds.extend(this.markers[i].getPosition());
            
        this.map.fitBounds(bounds);
    },
    
    showPointInfo: function (marker, idx) {
        var point = this.points[idx], 
            days = [];
        for (var i = 0; i < point.hours.length; i++) {
            days.push('<span class="dow">' + point.hours[i].day + '</span>' + point.hours[i].hours.join(', '));
        }
        this.infoWindow.setContent(
            '<div class="fermopoint-info-window">' +
            '<div class="row title">' + point.name + '</div>' +
            '<div class="row"></div>' +
            '<div class="row distance"><strong>' + Translator.translate('Distance') + ': </strong>' + point.distance + ' km </div>' +
            '<div class="row contact"><strong>' + Translator.translate('Contact') + ': </strong>' + point.contact + '</div>'+
            '<div class="row category"><strong>' + Translator.translate('Category') + ': </strong>' + point.category + '</div>'+
            '<div class="row hours"><!--strong>' + Translator.translate('Hours') + ': </strong--><div class="hours-list">' + days.join('<br />') + '</div></div>'+
            '<div class="row select"><a class="fermopoint-select-me" rel="' + idx + '" href="#">' + Translator.translate('Select this pick-up point') + '</a></div>'
        );
        this.infoWindow.open(this.map, marker);  
    },
	
	setPoints: function (points) {
        var i, marker, point, caller = this;
        for (i = 0; i < this.markers.length; i++)
            this.markers[i].setMap(null);
            
        this.clusterer.clearMarkers();
        this.markers.length = 0;
    
		this.points = points;
        if ( ! this.points.length)
            return;
        
        for (i = 0; i < this.points.length; i++) {
            point = this.points[i];
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(point.latitude, point.longitude),
                map: this.map,
                icon: this.mediaUrl + 'marker_point.png'
            });
            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function () {
                    caller.showPointInfo(marker, i);
                }
            })(marker, i));
            this.markers.push(marker);
        }
        
        this.clusterer.addMarkers(this.markers);
        
        this.rebound();
	},
    
    choosePoint: function (idx) {
        this.infoWindow.close();
        if (typeof(this.onSelectPoint) === 'function')
            this.onSelectPoint.call(this, this.points[idx]);
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
          
        if (typeof(this.onInit) === 'function')
            this.onInit.call(this);
    },
    
    setLocation: function (lat, lng) {
        if (this.location)
            this.location.setMap(null);
        this.location = new google.maps.Marker({
            position: new google.maps.LatLng(lat, lng),
            map: this.map
        });
        
        this.rebound();
    },
    
    search: function (address, radius) {
        var url = this.searchUrl,
            caller = this;
            
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
                caller.setPoints(json.points);
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
	
	setUseStorePickup: function(flag)
	{
		var url = this.changeMethodUrl;	
		
		if (flag)
			url += 'flag/1';
		else
			url += 'flag/0';
		
		var request = new Ajax.Request(url, {method: 'get', onFailure: ""}); 			
	}
	
}
