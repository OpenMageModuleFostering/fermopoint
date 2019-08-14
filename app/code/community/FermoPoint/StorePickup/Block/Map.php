<?php

class FermoPoint_StorePickup_Block_Map extends Mage_Core_Block_Template
{

	public function __construct()
	{	
		parent::__construct();
		
		$this->setTemplate('fpstorepickup/map.phtml');
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
        $parts = array(
            (is_array($street) ? implode(', ', $street) : $street) . ',',
            $address->getPostcode(),
            $address->getCity(),
        );
        
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
	
}
