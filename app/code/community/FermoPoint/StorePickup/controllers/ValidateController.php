<?php

class FermoPoint_StorePickup_ValidateController extends Mage_Core_Controller_Front_Action
{

    public function nicknameAction()
    {
        $nickname = $this->getRequest()->getPost('nickname', '');
        $api = Mage::getSingleton('fpstorepickup/api');
        if ( ! empty($nickname))
            $result = $api->isNicknameAvailable($nickname) ? 'ok' : 'error';
        else
            $result = 'ok';
        
        $this->getResponse()->setBody($result);
    }
    
    public function dobAction()
    {
        $nickname = $this->getRequest()->getPost('nickname', '');
        $dob = $this->getRequest()->getPost('dob', '');
        $dob = Mage::helper('fpstorepickup')->convertDate($dob);
        $api = Mage::getSingleton('fpstorepickup/api');
        if ( ! empty($nickname) && ! empty($dob))
            $result = $api->isNicknameAndDobMatch($nickname, $dob) ? 'ok' : 'error';
        else
            $result = 'ok';
        
        $this->getResponse()->setBody($result);
    }
    
}
