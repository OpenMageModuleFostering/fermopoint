;(function () {
    document.observe('dom:loaded', function() {
        var oldHandler = FireCheckout.OrderReview.prototype.update,
            oldResponseHandler = FireCheckout.prototype.setResponse;
        FireCheckout.OrderReview.prototype.update = function (from, to) {
            var target = to || from,
                review = $(target + '-review'),
                fpRadio = $('s_method_fpstorepickup_fpstorepickup');
                
            if (fpRadio && fpRadio.checked && target == 'shipping-address') {
                review && review.update(this.getTitle(target) + $('fermopoint_point_address').innerHTML);
            } else
                return oldHandler.apply(this, arguments);
        }
        
        FireCheckout.prototype.setResponse = function (response) {
            var result = oldResponseHandler.apply(this, arguments);
            try {
                fpStorePickup.forceMapUpdate();
                setTimeout(function () {
                    fpStorePickup.forceMapUpdate();
                }, 1);
                setTimeout(function () {
                    fpStorePickup.forceMapUpdate();
                }, 100);
            } catch (e) {
            }
            return result;
        }
    });
})();
