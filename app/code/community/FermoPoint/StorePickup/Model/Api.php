<?php

class FermoPoint_StorePickup_Model_Api {

    protected function _hideClientSecret($debugData, $clientSecret)
    {
        if (is_array($debugData) && strlen($clientSecret)) {
            foreach ($debugData as $key => &$value) {
                if (is_string($value))
                    $value = str_replace($clientSecret, '******', $value);
                else
                    $value = $this->_hideClientSecret($value, $clientSecret);
            }
        }
        return $debugData;
    }

    protected function _debug($debugData)
    {
        if (Mage::helper('fpstorepickup/config')->isDebug()) {
            $debugData = $this->_hideClientSecret($debugData, Mage::helper('fpstorepickup/config')->getClientSecret());
            Mage::getModel('core/log_adapter', 'fermopoint_storepickup.log')
               ->log($debugData);
        }
    }
    
    protected function _getToken($merchantId, $clientSecret, $body)
    {
        return hash_hmac("sha256", $body, $merchantId . $clientSecret);
    }
    
    protected function _signData($data, $clientId, $clientSecret)
    {
        if (empty($clientId))
            throw new FermoPoint_StorePickup_Exception(Mage::helper('fpstorepickup')->__('Please specify Client ID'));
        if (empty($clientSecret))
            throw new FermoPoint_StorePickup_Exception(Mage::helper('fpstorepickup')->__('Please specify Client Key'));
        $ts = gmdate('Y-m-d\TH:i:s') . substr((string) microtime(), 1, 8) . 'Z';
        return array(
            'client_id' => $clientId,
            'ts' => $ts,
            'auth_token' => hash_hmac('sha256', $ts, $clientId . $clientSecret),
            'data' => $data,
        );
    }
    
    protected function _buildUrl($base, $params)
    {
        return $base
            . (strpos($base, '?') !== false ? '' : '?')
            . http_build_query($params)
        ;
    }

    public function call($method, $data = array(), $params = array())
    {
        $config = Mage::helper('fpstorepickup/config');
        $signedData = $this->_signData($data, $config->getClientId(), $config->getClientSecret());
        $this->_debug($signedData);
        
        $client = new Zend_Http_Client($this->_buildUrl($config->getEndpointUrl($method), $params));
        $client->setConfig(array(
            'maxredirects' => 0,
            'timeout'      => 15,
        ));
        $client->setHeaders(array('Accept: text/json'));
        $client->setHeaders(array('Accept-Encoding: identity'));
        $client->setConfig(array('strictredirects' => true));
        $client->setRawData(json_encode($signedData), 'application/json');

        try {
            $response = $client->request(Zend_Http_Client::POST);
        } catch (Zend_Http_Client_Exception $e) {
            Mage::logException($e);
            throw new FermoPoint_StorePickup_Exception(Mage::helper('fpstorepickup')->__('Could not communicate with server'));
        }
        $body = $response->getBody();
        
        $json = json_decode($body, true);
        if ($json === null)
        {
            $this->_debug($body);
            throw new FermoPoint_StorePickup_Exception(Mage::helper('fpstorepickup')->__('Invalid server reply'));
        }
        
        $this->_debug($json);
            
        return $json;
    }
    
    public function getMerchant()
    {
        return $this->call('merchant');
    }
    
    public function validateMerchant()
    {
        $result = $this->call('merchant');
        
        return isset($result['credits']) && $result['credits'] > 0;
    }
    
    public function getOrdersCount()
    {
        $raw = $this->call('merchant');
        $result = 0;
        if (isset($raw['orders']))
            foreach ($raw['orders'] as $order)
                $result += $order['count'];
        return $result;
    }
    
    public function isNicknameAvailable($nickname)
    {
        return $this->call('users/nickname', array(
            'nickname' => $nickname,
        ));
    }
    
    public function isNicknameAndDobMatch($nickname, $dob)
    {
        return $this->call('users/check', array(
            'nickname' => $nickname,
            'born_date' => $dob,
        ));
    }
    
    public function isGuestNicknameAndDobMatch($nickname, $dob)
    {
        return $this->call('users/guest-check', array(
            'nickname' => $nickname,
            'born_date' => $dob,
        ));
    }
    
    public function isEmailAvailable($email)
    {
        return $this->call('users/email', array(
            'email' => $email,
        ));
    }
    
    public function getPoints($params)
    {
        $points = $this->call('points/search', $params);
        if ( ! is_array($points))
            return array();
         
        return $points;
    }
    
    public function submitOrder(Mage_Sales_Model_Order $order, FermoPoint_StorePickup_Model_Order_Point $orderPoint)
    {
        $data = array(
            'point_id' => $orderPoint->getPointId(),
            'merchant_id' => $order->getIncrementId(),
            'merchant_notes' => Mage::helper('fpstorepickup')->__('Order #%s', $order->getIncrementId()),
            'existing_user' => $orderPoint->getAccountType() == 'existing',
            'nickname' => $orderPoint->getNickname(),
            'email' => $orderPoint->getEmail(),
            'phone_number' => $orderPoint->getPhoneNumber(),
            'born_date' => $orderPoint->getDob(),
        );
        if ( ! $data['existing_user'])
        {
            $billingAddress = $order->getBillingAddress();
            $regionCode = null;
            if ($regionId = $billingAddress->getRegionId())
            {
                $region = Mage::getModel('directory/region')->load($regionId);
                if ($region->getId())
                    $regionCode = $region->getCode();
            }
            if ( ! isset($regionCode))
                $regionCode = $billingAddress->getRegion();
            
            $data['user'] = array(
                'nickname' => $orderPoint->getNickname(),
                'email' => $orderPoint->getEmail(),
                'phone_number' => $orderPoint->getPhoneNumber(),
                'full_name' => $order->getCustomerName(),
                'born_date' => $orderPoint->getDob(),
                'address' => array(
                    's' => $billingAddress->getStreetFull(),
                    'e' => null,
                    'c' => $billingAddress->getCity(),
                    'l' => null,
                    'p' => $billingAddress->getPostcode(),
                    'd' => $regionCode,
                ),
                //'tax_code' => 'AAAAAA11A11A111A',
                'newsletter' => true,
            );
        }
        $result = $this->call('booking/book', $data);
        
        if ( ! is_array($result) || ! isset($result['ticketId']))
            throw new FermoPoint_StorePickup_Exception(Mage::helper('fpstorepickup')->__('Could not submit order'));
            
        return $result['ticketId'];
    }
    
    protected function _parseTracking($data)
    {
        return array(
            'status' => $data['state'],
            'datetime' => $data['last_update'],
            'history' => isset($data['notes']) && is_array($data['notes']) ? $data['notes'] : array(),
        );
        return $data;
    }
    
    public function trackShipment($trackNumbers)
    {
        $result = array();
        foreach ($trackNumbers as $trackNumber)
        {
            try {
                $result[$trackNumber] = $this->_parseTracking($this->call('orders/order/' . $trackNumber));
            } catch (FermoPoint_StorePickup_Exception $e) {
                Mage::logException($e);
                $result[$trackNumber] = $e->getMessage();
            }
        }
        return $result;
    }
    
    public function approveOrderByTicketId($ticketId)
    {
        $this->call('booking/approve/' . $ticketId);
    }
    
    public function cancelOrderByTicketId($ticketId)
    {
        $this->call('booking/cancel/' . $ticketId);
    }
    
    public function getOrders($limit = 20, $offset = 0)
    {
        return $this->call('orders', array('take' => $limit, 'skip' => $offset, 'orderby' => 'date'), array());
    }
    
}
