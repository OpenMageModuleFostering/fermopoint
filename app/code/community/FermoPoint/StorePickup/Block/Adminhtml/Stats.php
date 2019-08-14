<?php

class FermoPoint_StorePickup_Block_Adminhtml_Stats extends Mage_Adminhtml_Block_Template {
    
    protected $_merchant;
    
    protected function _getMerchant()
    {
        if ($this->_merchant === null)
        {
            try {
                $this->_merchant = Mage::getModel('fpstorepickup/api')->getMerchant();
            } catch (FermoPoint_StorePickup_Exception $e) {
                $this->_merchant = false;
            }
        }
        return $this->_merchant;
    }
    
    public function getCredits()
    {
        $merchant = $this->_getMerchant();
        return $merchant !== false ? $merchant['credits'] : false;
    }
    
    public function getOrders()
    {
        $merchant = $this->_getMerchant();
        return $merchant !== false ? $merchant['orders'] : array();
    }

}
