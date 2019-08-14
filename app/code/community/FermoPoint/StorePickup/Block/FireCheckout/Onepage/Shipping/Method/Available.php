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
        if ($this->_storePickupAvailable)
            $html .= $this->getLayout()->createBlock('fpstorepickup/map')->toHtml();
        
        return $html;
    }

}

