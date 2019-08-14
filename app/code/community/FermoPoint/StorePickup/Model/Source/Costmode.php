<?php

class FermoPoint_StorePickup_Model_Source_Costmode {

    protected $_options;
    
    const MODE_FLAT = 'flat';
    const MODE_SUBTOTAL = 'subtotal';
    const MODE_WEIGHT = 'weight';

    public function toOptionArray()
	{
		if ( ! $this->_options)
        {
            $this->_options = array(
                array(
                    'label' => Mage::helper('fpstorepickup')->__('Flat Rate (same for all)'),
                    'value' => self::MODE_FLAT,
                ),
                array(
                    'label' => Mage::helper('fpstorepickup')->__('Based on Subtotal Value'),
                    'value' => self::MODE_SUBTOTAL,
                ),
                array(
                    'label' => Mage::helper('fpstorepickup')->__('Based on Total Weight'),
                    'value' => self::MODE_WEIGHT,
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
