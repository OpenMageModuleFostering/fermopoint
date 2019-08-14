<?php

class FermoPoint_StorePickup_IndexController extends Mage_Core_Controller_Front_Action
{

	public function changemethodAction()
	{	
        $flag = (bool) $this->getRequest()->getParam('flag');
        Mage::helper('fpstorepickup')->setUseMethod($flag);
	}
    
    public function searchAction()
    {
        $this->getResponse()->setHeader('Content-type', 'application/json');
    
        $response = new Varien_Object();
        $response->setError(false);
        
        try {
            $request = Mage::getModel('fpstorepickup/api_searchData');
            $request->loadFromPost($this->getRequest()->getPost());
            $response->setLatitude($request->getLatitude());
            $response->setLongitude($request->getLongitude());
            $response->setPoints(Mage::getSingleton('fpstorepickup/points')->getPoints($request));
        } catch (Mage_Core_Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        } catch (FermoPoint_StorePickup_Exception $e) {
            $response->setError(true);
            $response->setMessage($this->__('Error while communicating with FermoPoint. Please retry later.'));
        } catch (Exception $e) {
            Mage::logException($e);
            $response->setError(true);
            $response->setMessage($this->__('Unknown error. Please retry later.'));
        }
        
        $this->getResponse()->setBody($response->toJson());
    }
    
}
