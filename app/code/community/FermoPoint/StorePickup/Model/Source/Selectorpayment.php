<?php

class FermoPoint_StorePickup_Model_Source_Selectorpayment
{
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label' => Mage::helper('fpstorepickup')->__('All Allowed Payments')),
            array('value' => 1, 'label' => Mage::helper('fpstorepickup')->__('Specific Payments')),
        );
    }
}
