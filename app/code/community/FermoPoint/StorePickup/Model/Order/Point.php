<?php

class FermoPoint_StorePickup_Model_Order_Point extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('fpstorepickup/order_point');
    }
    
    public function createFrom(Mage_Sales_Model_Order $order, FermoPoint_StorePickup_Model_Point $point, $options)
    {
        $this->setData('order_id', $order->getId());
        $this->setData('point_id', $point->getId());
        $this->setData('point_name', $point->getName());
        $this->setData('point_address', $point->getFormattedAddress());
        $this->setData('point_data', $point->getData());
        $this->setData('is_approved', false);
        $this->setData('is_cancelled', false);
        $this->addData($options);
        $this->save();
        return $this;
    }
    
    public function getPoint()
    {
        return Mage::getModel('fpstorepickup/point')->setData($this->getPointData());
    }
    
}
