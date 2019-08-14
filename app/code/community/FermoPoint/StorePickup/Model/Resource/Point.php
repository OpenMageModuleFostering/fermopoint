<?php

class FermoPoint_StorePickup_Model_Resource_Point extends Mage_Core_Model_Mysql4_Abstract
{
    
    protected $_isPkAutoIncrement = false;

    protected $_serializableFields = array(
        'point_data' => array(
            array(),
            array(),
            false
        )
    );

    public function _construct()
    {    
        $this->_init('fpstorepickup/point', 'point_id');
    }
}
