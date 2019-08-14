<?php

class FermoPoint_StorePickup_Model_Resource_Order_Point_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('fpstorepickup/order_point');
    }
}
