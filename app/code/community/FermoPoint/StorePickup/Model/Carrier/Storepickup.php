<?php

class FermoPoint_StorePickup_Model_Carrier_Storepickup
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

    protected $_code = 'fpstorepickup';

   
	public function getCode()
	{
		return $this->_code;
	}
	
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {		
		if ( ! $this->getConfigFlag('active'))
            return false;
            
        if ( ! Mage::helper('fpstorepickup/config')->getTosAccepted())
            return false;
		
		$items = $request->getAllItems();
		if ( ! count($items))
			return false;
            
        if ($request->getDestCountryId() !== 'IT')
            return false;
            
        if ($this->getConfigData('maximum_weight') && ($request->getPackageWeight() > $this->getConfigData('maximum_weight')))
            return false;
            
        if ($this->getConfigData('maximum_subtotal') && ($request->getBaseSubtotalInclTax() > $this->getConfigData('maximum_subtotal')))
            return false;
            
        $api = Mage::getSingleton('fpstorepickup/api');
        try {
            $result = $api->validateMerchant();
        } catch (FermoPoint_StorePickup_Exception $e) {
            return false;
        }
        if ( ! $result)
            return false;
            
        $cost = Mage::helper('fpstorepickup')->getCost($request);
            
		$result = Mage::getModel('shipping/rate_result');
		$method = Mage::getModel('shipping/rate_result_method');
		$method->setCarrier('fpstorepickup');
		$method->setCarrierTitle($this->getConfigData('title'));
		$method->setMethod('fpstorepickup');
		$method->setMethodTitle('');
		$method->setPrice($cost);
		$method->setCost($cost);
		$result->append($method);
		
		return $result;
    }

    public function getAllowedMethods()
    {
        return array(
            'fpstorepickup' => 'fpstorepickup',
        );
    }
    
    public function getTrackingInfo($tracking)
    {
        $info = array();

        $result = $this->getTracking($tracking);

        if($result instanceof Mage_Shipping_Model_Tracking_Result){
            if ($trackings = $result->getAllTrackings()) {
                return $trackings[0];
            }
        }
        elseif (is_string($result) && !empty($result)) {
            return $result;
        }

        return false;
    }
    
    public function getTracking($trackings)
    {
        if ( ! is_array($trackings))
            $trackings = array($trackings);
            
        $errorTitle = Mage::helper('fpstorepickup')->__('Unable to retrieve tracking');
    
        $raw = Mage::getSingleton('fpstorepickup/api')->trackShipment($trackings);
        
        $success = array();
        $error = array();
        foreach ($raw as $idx => $row)
            if (is_array($row))
            {
                $progress = array();
                foreach ($row['history'] as $entry)
                {
                    $timestamp = strtotime($entry['date']);
                    $progress[] = array(
                        'deliverydate' => date('Y-m-d', $timestamp),
                        'deliverytime' => date('H:i:s', $timestamp),
                        'activity' => $entry['note'],
                        'deliverylocation' => Mage::helper('fpstorepickup')->__('Fermo!Point'),
                    );
                }
                $timestamp = strtotime($row['datetime']);
                $success[$idx] = array(
                    'deliverydate' => date('Y-m-d', $timestamp),
                    'deliverytime' => date('H:i:s', $timestamp),
                    'status' => Mage::helper('fpstorepickup')->__($row['status']),
                    'progressdetail' => $progress,
                );
            }
            else
                $error[$idx] = true;
                
        $result = Mage::getModel('shipping/tracking_result');
        if ($success || $error) {
            foreach ($error as $t => $r) {
                $error = Mage::getModel('shipping/tracking_result_error');
                $error->setCarrier('fpstorepickup');
                $error->setCarrierTitle($this->getConfigData('title'));
                $error->setTracking($t);
                $error->setErrorMessage($errorTitle);
                $result->append($error);
            }

            foreach ($success as $t => $data) {
                $tracking = Mage::getModel('shipping/tracking_result_status');
                $tracking->setCarrier('fpstorepickup');
                $tracking->setCarrierTitle($this->getConfigData('title'));
                $tracking->setTracking($t);
                $tracking->addData($data);

                $result->append($tracking);
            }
        } else {
            foreach ($trackings as $t) {
                $error = Mage::getModel('shipping/tracking_result_error');
                $error->setCarrier('fpstorepickup');
                $error->setCarrierTitle($this->getConfigData('title'));
                $error->setTracking($t);
                $error->setErrorMessage($errorTitle);
                $result->append($error);

            }
        }
        
        return $result;
    }
    
}
