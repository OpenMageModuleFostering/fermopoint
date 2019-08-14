<?php

class FermoPoint_StorePickup_Helper_Data extends Mage_Core_Helper_Abstract
{
        
    const SESSION_KEY = 'fermopoint_storepickup';
    
    protected function _getSessionData($key, $default = null)
    {
        $session = Mage::getSingleton('checkout/session');
        $data = $session->getData(self::SESSION_KEY);
        if (is_array($data) && isset($data[$key]))
            return $data[$key];
        else
            return $default;
    }
    
    protected function _setSessionData($key, $val)
    {
        $session = Mage::getSingleton('checkout/session');
        $data = $session->getData(self::SESSION_KEY);
        if ( ! is_array($data))
            $data = array();
        $data[$key] = $val;
        $session->setData(self::SESSION_KEY, $data);	
    }
        

    public function setUseMethod($flag)
    {
        $this->_setSessionData('use_storepickup', $flag);
    }
    
    public function getUseMethod()
    {
        return $this->_getSessionData('use_storepickup', false);
    }
    
    public function setPointId($pointId)
    {
        $this->setUseMethod(true);
        $this->_setSessionData('point_id', $pointId);
    }
    
    public function getPointId()
    {
        return $this->_getSessionData('point_id', 0);
    }
    
    public function setAccountType($type)
    {
        $this->_setSessionData('account', $type);
    }
    
    public function getAccountType()
    {
        return $this->_getSessionData('account', 'new');
    }
    
    public function setNickname($nickname)
    {
        $this->_setSessionData('nickname', $nickname);
    }
    
    public function getNickname()
    {
        return $this->_getSessionData('nickname', '');
    }
    
    public function setPhoneNumber($number)
    {
        $this->_setSessionData('phone_number', $number);
    }
    
    public function getPhoneNumber()
    {
        return $this->_getSessionData('phone_number', '');
    }
    
	public function getChangeMethodUrl()
	{
		return $this->_getUrl('fpstorepickup/index/changemethod', array('_secure' => true));		
	}
    
    public function getSearchUrl()
	{
		return $this->_getUrl('fpstorepickup/index/search', array('_secure' => true));		
	}
    
    public function getLocationUrl()
	{
		return $this->_getUrl('fpstorepickup/index/location', array('_secure' => true));		
	}
    
    public function getMediaUrl()
	{
		return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA, true) . 'fermopoint/';
	}
    
	public function getCustomerAddress()
	{
		$cSession = Mage::getSingleton('customer/session');

		$attribute = Mage::getModel("eav/entity_attribute")->load("customer_shipping_address_id","attribute_code");
					
		if($cSession->isLoggedIn() && $attribute->getId())
		{
			$address = Mage::helper('accountfield')
						->getShippingAddressByCustomerId($cSession->getCustomer()->getId());			
			if($address)
				return $address;
		}
		
		$cart = Mage::getSingleton('checkout/cart');
		return $cart->getQuote()->getShippingAddress();
	}
}
