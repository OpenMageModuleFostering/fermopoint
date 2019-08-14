<?php

class FermoPoint_StorePickup_Helper_Config extends Mage_Core_Helper_Abstract
{

    const API_VERSION = '1.1';
    
    const ENDPOINT_PRODUCTION = 'http://api.fermopoint.it/api/v:api_version/:api_method';
    const ENDPOINT_SANDBOX = 'http://sandbox.fermopoint.it/api/v:api_version/:api_method';

    const XML_PATH_ACCEPT = 'carriers/fpstorepickup/accept';
    const XML_PATH_COST_MODE = 'carriers/fpstorepickup/cost_mode';
    const XML_PATH_COST = 'carriers/fpstorepickup/cost';
    const XML_PATH_SUBTOTAL_COST = 'carriers/fpstorepickup/subtotal_cost';
    const XML_PATH_WEIGHT_COST = 'carriers/fpstorepickup/weight_cost';
    const XML_PATH_SANDBOX = 'carriers/fpstorepickup/sandbox';
    const XML_PATH_DEBUG = 'carriers/fpstorepickup/debug';
    const XML_PATH_CLIENTID = 'carriers/fpstorepickup/client_id';
    const XML_PATH_CLIENTSECRET = 'carriers/fpstorepickup/client_secret';
    const XML_PATH_TOSURL = 'carriers/fpstorepickup/tos_url';
    const XML_PATH_GMAPSKEY = 'carriers/fpstorepickup/gmaps_key';
    const XML_PATH_ALLOWSPECIFIC = 'carriers/fpstorepickup/allowspecific_payment';
    const XML_PATH_SPECIFICPAYMENTS = 'carriers/fpstorepickup/specificpayment';
    const XML_PATH_AUTOSHIP = 'carriers/fpstorepickup/auto_ship';
    const XML_PATH_GUEST = 'carriers/fpstorepickup/guest';
    const XML_PATH_GUEST_ONLY = 'carriers/fpstorepickup/guest_only';
    const XML_PATH_GUEST_NICKNAME = 'carriers/fpstorepickup/guest_nickname';
    const XML_PATH_GUEST_DOB = 'carriers/fpstorepickup/guest_dob';
    
    public function getAutoShip()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_AUTOSHIP);
    }
    
    public function getGuestEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_GUEST);
    }
    
    public function getGuestOnly()
    {
        return $this->getGuestEnabled() && Mage::getStoreConfigFlag(self::XML_PATH_GUEST_ONLY);
    }
    
    public function resetGuestEnabled()
    {
        Mage::getModel('core/config')->saveConfig(self::XML_PATH_GUEST, 0);
    }
    
    public function getTosAccepted()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ACCEPT);
    }
    
    public function getClientId($store_id)
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_CLIENTID, $store_id);
    }
    
    public function getGuestNickname()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_GUEST_NICKNAME);
    }
    
    public function getGuestDob()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_GUEST_DOB);
    }
    
    public function getAllowSpecificPayments()
    {
        return Mage::getStoreConfig(self::XML_PATH_ALLOWSPECIFIC);
    }
    
    public function getSpecificPayments()
    {
        return explode(',', Mage::getStoreConfig(self::XML_PATH_SPECIFICPAYMENTS));
    }
    
    public function getClientSecret($store_id)
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_CLIENTSECRET, $store_id);
    }
    
    public function isSandbox()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SANDBOX);
    }
    
    public function isDebug()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_DEBUG);
    }
    
    public function getCostMode()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_COST_MODE);
    }
    
    public function getCost()
    {
        return (float) Mage::getStoreConfig(self::XML_PATH_COST);
    }
    
    protected function _cmpRules($rule1, $rule2)
    {
        if ($rule1['value'] < $rule2['value'])
            return -1;
        elseif ($rule1['value'] > $rule2['value'])
            return 1;
        else
            return 0;
    }
    
    protected function _getCostRules($value, $key)
    {
        $rules = @unserialize($value);
        if (is_array($rules))
        {
            $result = array();
            foreach ($rules as $rule)
            {
                if ( ! isset($rule[$key]) || ! isset($rule['cost']))
                    continue;
                
                $value = (float) $rule[$key];
                if ($value <= 0)
                    continue;
                
                $cost = (float) $rule['cost'];
                $result[] = array(
                    'value' => $value, 
                    'cost' => $cost,
                );
            }
            
            if (count($rules))
                $result[] = array('value' => 0, 'cost' => 0);
                
            usort($result, array($this, '_cmpRules'));
        }
        else
            $result = array();
        return $result;
    }
    
    public function getSubtotalCost()
    {
        $value = Mage::getStoreConfig(self::XML_PATH_SUBTOTAL_COST);
        return $this->_getCostRules($value, 'subtotal');
    }
    
    public function getWeightCost()
    {
        $value = Mage::getStoreConfig(self::XML_PATH_WEIGHT_COST);
        return $this->_getCostRules($value, 'weight');
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
