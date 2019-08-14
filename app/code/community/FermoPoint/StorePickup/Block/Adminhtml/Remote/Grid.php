<?php

class FermoPoint_StorePickup_Block_Adminhtml_Remote_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_defaultSort = 'order_id';

    protected $_parentTemplate;

    public function __construct()
    {
        parent::__construct();
        $this->setId('remoteGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('order_id');
        $this->setSaveParametersInSession(true);
        $this->setFilterVisibility(false);
    }
    
    protected function _createCollection()
    {
        return Mage::getModel('fpstorepickup/api_orderCollection');
    }

    protected function _prepareCollection()
    {
        $collection = $this->_createCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('ticket_id', array(
            'header'    => $this->__('Ticket ID'),
            'index'     => 'ticketId',
            'filter'    => false,
            'sortable'  => false,
        ));
        
        $this->addColumn('merchant_id', array(
            'header'    => $this->__('Order ID'),
            'index'     => 'merchant_id',
            'filter'    => false,
            'sortable'  => false,
        ));
        
        $this->addColumn('merchant_notes', array(
            'header'    => $this->__('Merchant Notes'),
            'index'     => 'merchant_notes',
            'filter'    => false,
            'sortable'  => false,
        ));
        
        $this->addColumn('point_id', array(
            'header'    => $this->__('Point ID'),
            'index'     => 'point_id',
            'filter'    => false,
            'sortable'  => false,
        ));
        
        $this->addColumn('nickname', array(
            'header'    => $this->__('Nickname'),
            'index'     => 'nickname',
            'filter'    => false,
            'sortable'  => false,
        ));
        
        $this->addColumn('email', array(
            'header'    => $this->__('Email'),
            'index'     => 'email',
            'filter'    => false,
            'sortable'  => false,
        ));
        
        $this->addColumn('phone_number', array(
            'header'    => $this->__('Phone Number'),
            'index'     => 'phone_number',
            'filter'    => false,
            'sortable'  => false,
        ));
        
        $this->addColumn('state', array(
            'header'    => Mage::helper('fpstorepickup')->__('State'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'state',
            'type'      => 'options',
            'options'   => array(),
            'options'   => Mage::getSingleton('fpstorepickup/source_state')->toOptionMap(),
            'filter'    => false,
            'sortable'  => false,
        ));
        
        $this->addColumn('notes', array(
            'header'    => Mage::helper('fpstorepickup')->__('Notes'),
            'index'     => 'notes',
            'renderer'  => 'fpstorepickup/adminhtml_remote_grid_renderer_notes',
            'filter'    => false,
            'sortable'  => false,
        ));
        
        $this->addColumn('last_update', array(
            'header'    => Mage::helper('fpstorepickup')->__('Last Update'),
            'index'     => 'last_update',
            'type'      => 'datetime',
            'filter'    => false,
            'sortable'  => false,
        ));
        
        $this->addExportType('*/*/exportCsv', Mage::helper('fpstorepickup')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('fpstorepickup')->__('Excel XML'));

        return parent::_prepareColumns();
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('log_id');
        
        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return 'javascript:void()';
    }

    public function getMainButtonsHtml()
    {
        return '';
    }

}
