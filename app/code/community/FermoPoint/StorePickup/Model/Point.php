<?php

class FermoPoint_StorePickup_Model_Point extends Mage_Core_Model_Abstract {

    public function _construct()
    {
        parent::_construct();
        $this->_init('fpstorepickup/point');
    }
    
    protected function _getPointData($index, $default = null)
    {
        $data = $this->getPointData();
        return isset($data[$index]) ? $data[$index] : $default;
    }
    
    protected function _getPointAddressData($index, $default = null)
    {
        $address = $this->_getPointData('a', array());
        return isset($address[$index]) ? $address[$index] : $default;
    }
    
    public function setPointData($data)
    {
        $this->setData('point_data', $data);
        $this->setName($this->getName());
        $this->setAddress($this->getFormattedAddress());
        return $this;
    }

    public function getName()
    {
        return $this->_getPointData('n', 'Point #' . $this->getId());
    }
    
    public function getStreet()
    {
        $parts = array();
        foreach (array('s', 'e') as $key)
        {
            $value = trim($this->_getPointAddressData($key));
            if ( ! empty($value))
            {
                $value = ' ' . $value;
                $parts[] = $value;
            }
        }
        return trim(implode('', $parts));
    }
    
    public function getCity()
    {
        return $this->_getPointAddressData('c');
    }
    
    public function getPostcode()
    {
        return $this->_getPointAddressData('p');
    }
    
    public function getRegion()
    {
        return $this->_getPointAddressData('d');
    }
    
    public function getRegionId()
    {
        $regionModel = Mage::getModel('directory/region')->loadByCode($this->getRegion(), $this->getCountryId());
        return $regionModel->getId();
    }
    
    public function getCountryId()
    {
        // hardcoded
        return 'IT';
    }
    
    public function getLatitude()
    {
        return $this->_getPointData('lt');
    }
    
    public function getLongitude()
    {
        return $this->_getPointData('ln');
    }
    
    public function getDistance()
    {
        return $this->_getPointData('d');
    }
    
    protected function _formatDay($day)
    {
        $days = Mage::app()->getLocale()->getTranslationList('days');
        $map = array(
            'sun',
            'mon',
            'tue',
            'wed',
            'thu',
            'fri',
            'sat',
        );
        return $days['format']['abbreviated'][$map[$day]];
    }
    
    protected function _formatHour($hour)
    {
        return sprintf('%02d:%02d', (int) $hour, abs($hour - (int) $hour) * 60);
    }
    
    public function getHours()
    {
        $rows = $this->_getPointData('o', array());
        $result = array();
        foreach ($rows as $row)
        {
            $hours = array();
            foreach ($row['h'] as $range)
                $hours[] = $this->_formatHour($range['o']) . '&ndash;' . $this->_formatHour($range['c']);
            $result[] = array(
                'day' => $this->_formatDay($row['d']),
                'hours' => $hours,
            );
        }
        return $result;
    }
    
    public function getContactPerson()
    {
        return $this->_getPointData('p');
    }
    
    public function getCategory()
    {
        return $this->_getPointData('s');
    }
    
    public function getAddressData()
    {
        return array(
            'firstname' => Mage::helper('fpstorepickup')->__('Fermo!Point'),
			'lastname' => $this->getName(),
            'company' => '',
			'street' => $this->getStreet(),
			'city' => $this->getCity(),
			'region' => $this->getRegion(),
			'region_id' => $this->getRegionId(),
			'postcode' => $this->getPostcode(),
			'country_id' => $this->getCountryId(),
			'telephone' => $this->getTelephone(),
        );
    }
    
    public function getFormattedAddress()
    {
        $address = Mage::getModel('customer/address');
        $address->setData($this->getAddressData());
        
        return $address->format('oneline');
    }
    
    public function toArray(array $arrAttributes = array())
    {
        $result = array(
            'id' => $this->getId(),
            'name' => $this->getName(),
        );
        $result = array_merge($result, $this->getAddressData());
        $result = array_merge($result, array(
            'latitude' => $this->getLatitude(),
            'longitude' => $this->getLongitude(),
            'distance' => $this->getDistance(),
            'hours' => $this->getHours(),
            'contact' => $this->getContactPerson(),
            'category' => $this->getCategory(),
        ));
        
        return $result;
    }
    
}
