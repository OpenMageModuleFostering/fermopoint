<?php

class FermoPoint_StorePickup_Model_Api_SearchData extends Varien_Object {

    protected function _validate()
    {
        if (( ! Zend_Validate::is($this->getAddress(), 'NotEmpty')
            && ( ! Zend_Validate::is($this->getLatitude(), 'NotEmpty')
               || ! Zend_Validate::is($this->getLongitude(), 'NotEmpty')
            )) || ! Zend_Validate::is($this->getRadius(), 'NotEmpty')
        ) 
            Mage::throwException(Mage::helper('fpstorepickup')->__('Invalid search parameters.'));
            
        return $this;
    }
    
    protected function _searchLocation()
    {
        $address = $this->getAddress();
        if (empty($address))
            return $this;
        
        $location = Mage::getSingleton('fpstorepickup/googleMaps')->getLocation($this->getAddress());
        if ($location === null)
            Mage::throwException(Mage::helper('fpstorepickup')->__('Location does not found'));
        list($lat, $lng) = $location;
        $this->setLatitude($lat);
        $this->setLongitude($lng);
        
        return $this;
    }

    public function loadFromPost($raw)
    {
        $this
            ->setData($raw)
            ->_validate()
            ->_searchLocation();
        
        return $this;
    }
    
    public function compare($with)
    {
        $src = $this->toApi();
        $result = true;
        foreach ($src as $key => $val)
            if ( ! array_key_exists($key, $with) || $with[$key] != $val)
            {
                $result = false;
                break;
            }
        return $result;
    }

    public function toApi()
    {
        $result = array(
            'lat' => $this->getLatitude(),
            'lng' => $this->getLongitude(),
            'radius' => (int) $this->getRadius(),
            'day' => $this->hasData('day') ? (int) $this->getDay() : null,
            'from' => $this->hasData('from') ? (int) $this->getFrom() : 0,
            'to' => $this->hasData('to') ? (int) $this->getTo() : 24,
        );
        
        return $result;
    }
    
}
