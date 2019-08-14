<?php

class FermoPoint_StorePickup_Model_Api_OrderCollection extends Varien_Data_Collection {
    
    protected $_size = null;
    
    public function getSize()
    {
        if ($this->_size === null)
        {
            try {
                $this->_size = Mage::getSingleton('fpstorepickup/api')->getOrdersCount();
            } catch (FermoPoint_StorePickup_Exception $e) {
                $this->_size = 1;
            }
        }
        return $this->_size;
    }
    
    public function loadData($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        $limit = $this->getPageSize();
        $offset = $this->getCurPage();
        try {
            $raw = Mage::getSingleton('fpstorepickup/api')->getOrders($limit, ($offset - 1) * $limit);
        } catch (FermoPoint_StorePickup_Exception $e) {
            $raw = array(
                array(
                    'email' => Mage::helper('fpstorepickup')->__('Service is not available at the moment, please try again later'),
                )
            );
        }
        foreach ($raw as $row)
        {
            $obj = new Varien_Object();
            $obj->setData($row);
            $this->addItem($obj);
        }
        $this->_setIsLoaded();
        return $this;
    }
    
}
