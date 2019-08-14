<?php

class FermoPoint_StorePickup_Helper_Config extends Mage_Core_Helper_Abstract
{

    const API_VERSION = '0.9';
    
    const ENDPOINT_PRODUCTION = 'http://api.fermopoint.it/api/v:api_version/:api_method';
    const ENDPOINT_SANDBOX = 'http://sandbox.fermopoint.it/api/v:api_version/:api_method';

    const XML_PATH_ACCEPT = 'carriers/fpstorepickup/accept';
    const XML_PATH_COST = 'carriers/fpstorepickup/cost';
    const XML_PATH_SANDBOX = 'carriers/fpstorepickup/sandbox';
    const XML_PATH_DEBUG = 'carriers/fpstorepickup/debug';
    const XML_PATH_CLIENTID = 'carriers/fpstorepickup/client_id';
    const XML_PATH_CLIENTSECRET = 'carriers/fpstorepickup/client_secret';
    const XML_PATH_TOSURL = 'carriers/fpstorepickup/tos_url';
    const XML_PATH_GMAPSKEY = 'carriers/fpstorepickup/gmaps_key';
    const XML_PATH_ALLOWSPECIFIC = 'carriers/fpstorepickup/allowspecific_payment';
    const XML_PATH_SPECIFICPAYMENTS = 'carriers/fpstorepickup/specificpayment';
    const XML_PATH_AUTOSHIP = 'carriers/fpstorepickup/auto_ship';
    
    public function getAutoShip()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_AUTOSHIP);
    }
    
    public function getTosAccepted()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ACCEPT);
    }
    
    public function getClientId()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_CLIENTID);
    }
    
    public function getAllowSpecificPayments()
    {
        return Mage::getStoreConfig(self::XML_PATH_ALLOWSPECIFIC);
    }
    
    public function getSpecificPayments()
    {
        return explode(',', Mage::getStoreConfig(self::XML_PATH_SPECIFICPAYMENTS));
    }
    
    public function getClientSecret()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_CLIENTSECRET);
    }
    
    public function isSandbox()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SANDBOX);
    }
    
    public function isDebug()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_DEBUG);
    }
    
    public function getCost()
    {
        return (float) Mage::getStoreConfig(self::XML_PATH_COST);
    }
    
    public function getTosUrl()
    {
        return Mage::getStoreConfig(self::XML_PATH_TOSURL);
    }
    
    public function getGMapsKey()
    {
        return Mage::getStoreConfig(self::XML_PATH_GMAPSKEY);
    }
    
    public function getEndpointUrl($method)
    {
        return strtr( ! $this->isSandbox() ? self::ENDPOINT_PRODUCTION : self::ENDPOINT_SANDBOX, array(
            ':api_version' => self::API_VERSION,
            ':api_method' => $method,
        ));
    }
    
}
