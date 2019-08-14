<?php

class FermoPoint_StorePickup_Block_Map extends Mage_Core_Block_Template
{
    
    protected $_initiallyHidden = false;

	public function __construct()
	{	
		parent::__construct();
		
		$this->setTemplate('fpstorepickup/map.phtml');
	}
    
    public function getInitiallyHidden()
    {
        return $this->_initiallyHidden;
    }
    
    public function setInitiallyHidden($flag)
    {
        $this->_initiallyHidden = $flag;
        return $this;
    }
    
    public function getTosUrl()
    {
        return Mage::helper('fpstorepickup/config')->getTosUrl();
    }
    
    public function getGoogleMaps($callback = null)
    {
        $params = array(
            'v' => '3.exp',
            'region' => 'it',
        );
        if ($apiKey = Mage::helper('fpstorepickup/config')->getGMapsKey())
            $params['key'] = $apiKey;
        if ($callback)
            $params['callback'] = $callback;
        return 'https://maps.googleapis.com/maps/api/js?' . http_build_query($params);
    }
    
    public function getBillingAddress()
    {
        $address = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress();
        $street = $address->getStreet();
        $street = is_array($street) ? implode(', ', $street) : $street;
        $parts = array();
        if ( ! empty($street))
        {
            $street .= ',';
            $parts[] = $street;
        }
        
        $postcode = $address->getPostcode();
        if ( ! empty($postcode))
            $parts[] = $postcode;
          
        $city = $address->getCity();
        if ( ! empty($city))
            $parts[] = $city;
        return implode(' ', $parts);
    }
    
    public function getUserPhone()
    {
        $address = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress();
        return $address->getTelephone();
    }
    
    public function getUserEmail()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getCustomerEmail();
    }
    
    public function getUserDob()
    {
        return '';
    }
	
}
