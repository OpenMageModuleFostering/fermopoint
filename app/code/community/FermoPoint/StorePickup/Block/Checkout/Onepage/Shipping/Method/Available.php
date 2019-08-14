<?php

class FermoPoint_StorePickup_Block_Checkout_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available {

    protected $_storePickupAvailable = false;

    protected function _filterGroups($groups)
    {
        $useStorePickup = Mage::helper('fpstorepickup')->getUseMethod();
        foreach ($groups as $group => $rates)
        {
            if ($group === 'fpstorepickup' && $useStorePickup)
                $this->_storePickupAvailable = true;
        
            if ($group !== 'fpstorepickup' && $useStorePickup
                || $group === 'fpstorepickup' && ! $useStorePickup
            )
                unset($groups[$group]);
        }
        
        return $groups;
    }

    public function getShippingRates()
    {
        if (empty($this->_rates)) {
            $this->getAddress()->collectShippingRates()->save();

            $groups = $this->getAddress()->getGroupedAllShippingRates();

            $this->_rates = $this->_filterGroups($groups);
        }
        
        return $this->_rates;
    }
    
    protected function _afterToHtml($html)
    {
        if ($this->_storePickupAvailable && ! Mage::helper('fpstorepickup')->getIsOneStepCheckout())
            $html .= $this->getLayout()->createBlock('fpstorepickup/map')->toHtml();
        elseif (Mage::helper('fpstorepickup')->getIsOneStepCheckout())
        {
            $flag = Mage::helper('core')->jsonEncode($this->_storePickupAvailable);
            $html .= <<<JS
<script type="text/javascript">
    (function () {
        var flag = {$flag},
            container = $('fermopoint_outer');
        if ( ! container)
            return;
        if (flag)
            container.show();
        else
            container.hide();
    })();
</script>
JS
            ;
        }
        
        return $html;
    }

}

