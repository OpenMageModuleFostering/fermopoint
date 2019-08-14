<?php

class FermoPoint_StorePickup_Block_Adminhtml_Remote extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_remote';
        $this->_blockGroup = 'fpstorepickup';
        $this->_headerText = Mage::helper('fpstorepickup')->__('All Orders');
        parent::__construct();
        $this->removeButton('add');
    }

}
