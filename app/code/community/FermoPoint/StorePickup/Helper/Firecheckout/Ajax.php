<?php

class FermoPoint_StorePickup_Helper_Firecheckout_Ajax extends TM_FireCheckout_Helper_Ajax {
    
    public function getIsShippingMethodDependsOn($section)
    {
        $stack = debug_backtrace(true);
        foreach ($stack as $line)
        {
            if ( ! isset($line['object']) || ! $line['object'] instanceof TM_FireCheckout_IndexController)
                continue;
            
            if ( ! isset($line['function']) || $line['function'] != 'saveShippingMethodAction')
                continue;
            
            return false;
        }
        return $this->getIsSectionDependsOn('shipping-method', $section);
    }
    
}
