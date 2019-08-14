<?php

class FermoPoint_StorePickup_Block_Adminhtml_Configuration_Disabled extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $element->setReadonly(true);
        return parent::_getElementHtml($element);
    }

}
