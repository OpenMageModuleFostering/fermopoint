<?php

class FermoPoint_StorePickup_Model_Api_SearchData extends Varien_Object {

    protected function _validate()
    {
        if ( ! Zend_Validate::is($this->getAddress(), 'NotEmpty')
            || ! Zend_Validate::is($this->getRadius(), 'NotEmpty')
        ) 
            Mage::throwException(Mage::helper('fpstorepickup')->__('Invalid search parameters.'));
            
        return $this;
    }
    
    protected function _searchLocation()
    {
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
            if ( ! isset($with[$key]) || $with[$key] != $val)
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
            'radius' => $this->getRadius(),
            'day' => (int) $this->getDay(),
            'from' => (int) $this->getFrom(),
            'to' => $this->getTo() ? (int) $this->getTo() : 24,
        );
        
        return $result;
    }
    
}
