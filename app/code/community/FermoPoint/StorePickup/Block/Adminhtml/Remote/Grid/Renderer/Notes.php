<?php

class FermoPoint_StorePickup_Block_Adminhtml_Remote_Grid_Renderer_Notes extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        if ( ! is_array($row->getNotes()) || ! count($row->getNotes()))
            return '';
        $block = $this->getLayout()->createBlock('adminhtml/widget_grid_column_renderer_datetime');
        $column = new Varien_Object();
        $column->setData(array(
            'getter' => null,
            'index' => 'date',
        ));
        $block->setColumn($column);
        $result = '<table>';
        $result .= '<thead>';
        $result .= '<tr class="headings"><th>'.$this->__('Date').'</th><th>'.$this->__('Note').'</th></tr>';
        $result .= '</thead>';
        $result .= '<tbody>';
        foreach ($row->getNotes() as $entry)
        {
            $data = new Varien_Object();
            $data->setData($entry);
            $result .= '<tr><td>'.$block->render($data).'</td><td>'.$this->escapeHtml($entry['note']).'</td></tr>';
        }
        $result .= '</tbody>';
        $result .= '</table>';
        return $result;
    }
    
    public function renderExport(Varien_Object $row)
    {
        $result = array();
        foreach ($row->getNotes() as $entry)
            $result[] = $entry['date'] . ': ' . $entry['note'];
        return implode('; ', $result);
    }
    
}
