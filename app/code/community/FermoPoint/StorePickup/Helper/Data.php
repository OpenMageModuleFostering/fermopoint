<?php

class FermoPoint_StorePickup_Helper_Data extends Mage_Core_Helper_Abstract
{
        
    const SESSION_KEY = 'fermopoint_storepickup';
    
    protected $_isOneStepCheckout;
    
    public function getIsOneStepCheckout()
    {
        if ($this->_isOneStepCheckout === null)
        {
            $this->_isOneStepCheckout = false;
            if ('true' == (string)Mage::getConfig()->getNode('modules/NCR_ProductLists/active'))
            {
                $helper = Mage::helper('firecheckout');
                if ($helper !== false)
                {
                    $this->_isOneStepCheckout = $helper->canFireCheckout();
                }
            }
        }
        return $this->_isOneStepCheckout;
    }
    
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
    
    public function setDob($dob)
    {
        $this->_setSessionData('dob', $dob);
    }
    
    public function getDob()
    {
        return $this->_getSessionData('dob', '');
    }
    
	public function getChangeMethodUrl()
	{
		return $this->_getUrl('fpstorepickup/index/changemethod', array('_secure' => true));		
	}
    
    public function getSearchUrl()
	{
		return $this->_getUrl('fpstorepickup/index/search', array('_secure' => true));		
	}
    
    public function getValidateNicknameUrl()
	{
		return $this->_getUrl('fpstorepickup/validate/nickname', array('_secure' => true));		
	}
    
    public function getValidateDobUrl()
	{
		return $this->_getUrl('fpstorepickup/validate/dob', array('_secure' => true));		
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
    
    public function convertDate($date)
    {
        $locale = Mage::app()->getLocale();
        $dateObj = $locale->date(null, null, $locale->getLocaleCode(), false);

        $dateObj->setTimezone(
            Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE)
        );

        //set begining of day
        $dateObj->setHour(00);
        $dateObj->setMinute(00);
        $dateObj->setSecond(00);

        try {
            //set date with applying timezone of store
            $dateObj->set($date, Zend_Date::DATE_SHORT, $locale->getLocaleCode());
        } catch (Exception $e) {
            return null;
        }

        //convert store date to default date in UTC timezone without DST
        //$dateObj->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
        
        return $dateObj->toString('yyyy-MM-dd');
    }
    
    protected function _getRulesCost($rules, $value)
    {
        $result = 0;
        if (count($rules))
        {
            for ($i = 0; $i < count($rules) - 1; $i++)
            {
                $rule = $rules[$i];
                $nextRule = $rules[$i + 1];
                if ($value > $rule['value'] && $value <= $nextRule['value'])
                {
                    $result = $nextRule['cost'];
                    break;
                }
            }
        }
        return $result;
    }
    
    public function getCost(Mage_Shipping_Model_Rate_Request $request)
    {
        $config = Mage::helper('fpstorepickup/config');
        switch ($config->getCostMode())
        {
            case FermoPoint_StorePickup_Model_Source_Costmode::MODE_WEIGHT:  
                $weight = $request->getPackageWeight();
                $rules = $config->getWeightCost();
                $result = $this->_getRulesCost($rules, $weight);
                break;
            case FermoPoint_StorePickup_Model_Source_Costmode::MODE_SUBTOTAL:  
                $subtotal = $request->getBaseSubtotalInclTax();
                $rules = $config->getSubtotalCost();
                $result = $this->_getRulesCost($rules, $subtotal);
                break;
            case FermoPoint_StorePickup_Model_Source_Costmode::MODE_FLAT:
            default:
                $result = $config->getCost();
                
                
        }
        return $result;
    }
    
}
