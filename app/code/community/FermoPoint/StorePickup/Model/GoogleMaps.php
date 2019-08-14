<?php

class FermoPoint_StorePickup_Model_GoogleMaps
{
	const GEOCODER_URL = 'http://maps.google.com/maps/api/geocode/json?';
	const STATUS_OK = 'ok';
    
    const CACHE_TAG = 'fermopoint_gmaps';
    const CACHE_KEY = 'fermopoint_gmaps_location_%s';
    const CACHE_LIFETIME = 604800; // 1 week
    const CACHE_LIFETIME_ERROR = 300; // 5 mins
    
    protected function getLocationFromGoogle($address)
    {
        $params = array(
            'sensor' => 'false',
            'address' => $address,
        );
        if ($apiKey = Mage::helper('fpstorepickup/config')->getGMapsKey())
            $params['key'] = $apiKey;
        
        $url = self::GEOCODER_URL . http_build_query($params);
        
        $client = new Zend_Http_Client($url);
        $client->setHeaders(array('Accept-encoding: identity'));
        $client->setConfig(array('strictredirects' => true));

        try {
            $response = $client->request(Zend_Http_Client::POST);
        } catch (Zend_Http_Client_Exception $e) {
            Mage::logException($e);
            return null;
        }
        
        $body = $response->getBody();
        $json = json_decode($body, true);
        if ($json === null)
            return null;
            
        if ( ! isset($json['status']) || strtolower($json['status']) !== self::STATUS_OK)
            return null;
            
        if ( ! isset($json['results']) || ! is_array($json['results']) || ! count($json['results']))
            return null;
            
        $result = reset($json['results']);
        return array(
            $result['geometry']['location']['lat'],
            $result['geometry']['location']['lng'],
        );
    }
    
    public function getLocation($address)
    {
        $cacheKey = sprintf(self::CACHE_KEY, md5($address));
        $cache = Mage::app()->getCache();
        $value = unserialize($cache->load($cacheKey));
        if ( ! is_array($value) || $value['address'] != $address)
        {
            $value = array(
                'address' => $address,
                'location' => $this->getLocationFromGoogle($address),
            );
            
            $cache->save(
                serialize($value), 
                $cacheKey, 
                array(self::CACHE_TAG), 
                $value['location'] !== null ? self::CACHE_LIFETIME : self::CACHE_LIFETIME_ERROR
            );
        }
        return $value['location'];
    }
    
}
