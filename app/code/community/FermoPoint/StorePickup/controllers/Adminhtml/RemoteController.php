<?php

class FermoPoint_StorePickup_Adminhtml_RemoteController extends Mage_Adminhtml_Controller_Action {

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/fermopoint');
    }

    public function indexAction()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->loadLayout();
        $this->_addBreadcrumb($this->__('Orders'), $this->__('Orders'));
        $this->_addBreadcrumb($this->__('All Orders'), $this->__('All Orders'));
        $this->renderLayout();
    }
    
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function exportCsvAction()
    {
        $fileName   = 'orders.csv';
        $content    = $this->getLayout()->createBlock('fpstorepickup/adminhtml_remote_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'orders.xml';
        $content    = $this->getLayout()->createBlock('fpstorepickup/adminhtml_remote_grid')
            ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }
    
}
