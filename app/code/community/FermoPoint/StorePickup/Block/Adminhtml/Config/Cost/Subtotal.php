<?php

class FermoPoint_StorePickup_Block_Adminhtml_Config_Cost_Subtotal extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    
    public function getHtmlId()
    {
        return 'carriers_fpstorepickup_subtotal_cost';
    }
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('fpstorepickup/array.phtml');
    }
    
    public function _prepareToRender()
    {
        $this->addColumn('subtotal', array(
            'label' => Mage::helper('fpstorepickup')->__('Subtotal (less or equal)'),
            'style' => 'width:100px',
        ));
        $this->addColumn('cost', array(
            'label' => Mage::helper('fpstorepickup')->__('Shipping Cost'),
            'style' => 'width:100px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('fpstorepickup')->__('Add Rule');
    }
}
