<?php

class FermoPoint_StorePickup_Block_Adminhtml_Configuration_Manual extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _decorateRowHtml($element, $html)
    {
        return '<tr id="row_' . $element->getHtmlId() . '">' . $html . '</tr>';
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $message = $this->__('For all the information on installing and configuring the module and FermoPoint Merchant Account, you can refer to <a href="%s" target="_blank">this page</a>', 'https://www.fermopoint.it/guide/magento');
        $html = '<td class="label" colspan="4">' . $message . '</td>';
        return $this->_decorateRowHtml($element, $html);
    }

}
