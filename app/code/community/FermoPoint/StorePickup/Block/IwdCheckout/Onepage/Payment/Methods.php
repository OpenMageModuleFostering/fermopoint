<?php

class FermoPoint_StorePickup_Block_Checkout_Onepage_Payment_Methods extends Mage_Checkout_Block_Onepage_Payment_Methods
{
    public function getMethods()
    {
        $methods = $this->getData('methods');
        if (is_null($methods)) 
		{
            $store = $this->getQuote() ? $this->getQuote()->getStoreId() : null;
            $methods = $this->helper('payment')->getStoreMethods($store, $this->getQuote());
            foreach ($methods as $key => $method) {
                if ($this->_canUseMethod($method)) {
                    $this->_assignMethod($method);
                }
                else {
                    unset($methods[$key]);
                }
            }
            
            if ($this->getQuote()->getShippingAddress()->getShippingMethod() === 'fpstorepickup_fpstorepickup'
                && Mage::helper('fpstorepickup/config')->getAllowSpecificPayments()
            )
            {
                $allowed = array_flip(Mage::helper('fpstorepickup/config')->getSpecificPayments());
                foreach ($methods as $key => $method)
                {
                    if ( ! isset($allowed[$method->getCode()]))
                        unset($methods[$key]);
                }
                        
                $methods = array_values($methods);
            }
            
            $this->setData('methods', $methods);
        }
		return $methods;
    }
}