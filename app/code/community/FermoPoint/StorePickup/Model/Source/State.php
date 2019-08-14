<?php

class FermoPoint_StorePickup_Model_Source_State {

    protected $_options;

    public function toOptionArray()
	{
		if ( ! $this->_options)
        {
            $this->_options = array(
                array(
                    'label' => Mage::helper('fpstorepickup')->__('Init'),
                    'value' => 'Init',
                ),
                array(
                    'label' => Mage::helper('fpstorepickup')->__('Waiting For Payment'),
                    'value' => 'WaitingForPayment',
                ),
                array(
                    'label' => Mage::helper('fpstorepickup')->__('Waiting For Payment Confirm'),
                    'value' => 'WaitingForPaymentConfirm',
                ),
                array(
                    'label' => Mage::helper('fpstorepickup')->__('Waiting For Payment Capture'),
                    'value' => 'WaitingForPaymentCapture',
                ),
                array(
                    'label' => Mage::helper('fpstorepickup')->__('Booked'),
                    'value' => 'Booked',
                ),
                array(
                    'label' => Mage::helper('fpstorepickup')->__('Arrived'),
                    'value' => 'Arrived',
                ),
                array(
                    'label' => Mage::helper('fpstorepickup')->__('Collected'),
                    'value' => 'Collected',
                ),
                array(
                    'label' => Mage::helper('fpstorepickup')->__('Canceled'),
                    'value' => 'Canceled',
                ),
                array(
                    'label' => Mage::helper('fpstorepickup')->__('Error'),
                    'value' => 'Error',
                ),
                
            );
        }
        
        return $this->_options;
	}
    
    public function toOptionMap()
    {
        $result = array();
        foreach ($this->toOptionArray() as $option)
            $result[$option['value']] = $option['label'];
        return $result;
    }
    
}
