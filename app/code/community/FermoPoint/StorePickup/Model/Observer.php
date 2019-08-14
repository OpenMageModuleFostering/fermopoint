<?php

class FermoPoint_StorePickup_Model_Observer
{
    
    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }
    
    protected function _returnError($controller, $message)
    {
        $result = array(
            'error' => -1, 
            'message' => $message,
        );
        $controller->getOnepage()->getQuote()->collectTotals()->save();
        $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
    }
    
    public function onSaveShippingMethodBefore($observer)
    {
        $controller = $observer->getEvent()->getControllerAction();
        $request = $controller->getRequest();
        if ( ! $request->isPost())
            return;
        
        if ( ! Mage::helper('fpstorepickup')->getUseMethod(false))
            return;
        
        $pointId = $request->getPost('fermopoint_point_id', 0);
        $point = Mage::getModel('fpstorepickup/point')->load($pointId);
        if ( ! $point->getId())
            return $this->_returnError($controller, Mage::helper('fpstorepickup')->__('Unknown Fermo!Point ID'));
        
        $nickname = trim($request->getPost('fermopoint_nickname', ''));
        if (empty($nickname))
            return $this->_returnError($controller, Mage::helper('fpstorepickup')->__('Invalid Nickname'));
        
        $api = Mage::getModel('fpstorepickup/api');
        switch ($accountType = $request->getPost('fermopoint_account', 'new'))
        {
            case 'existing':
                if ($api->isNicknameAvailable($nickname))
                    return $this->_returnError($controller, Mage::helper('fpstorepickup')->__('There is no user with this nickname'));
                break;
            
            case 'new':
            default:
                $email = $this->getQuote()->getCustomerEmail();
                if ( ! $api->isEmailAvailable($email))
                    return $this->_returnError($controller, Mage::helper('fpstorepickup')->__('Your email is already registered on Fermo!Point'));
                
                if ( ! $api->isNicknameAvailable($nickname))
                    return $this->_returnError($controller, Mage::helper('fpstorepickup')->__('User with this nickname already exists'));
        }
        
        /*$telephone = trim($request->getPost('fermopoint_phone', ''));
        if (empty($telephone))
            return $this->_returnError($controller, Mage::helper('fpstorepickup')->__('Invalid phone number'));
        */
    }
	
	public function onSaveShippingMethodAfter($event)
	{
        $quote = $event->getQuote();
        if ($quote->getShippingAddress()->getShippingMethod() !== 'fpstorepickup_fpstorepickup')
        {
            Mage::helper('fpstorepickup')->setUseMethod(false);
            return;
        }
        $request = $event->getRequest();
        $pointId = $request->getPost('fermopoint_point_id');
        Mage::helper('fpstorepickup')->setPointId($pointId);
        Mage::helper('fpstorepickup')->setNickname(trim($request->getPost('fermopoint_nickname')));
        Mage::helper('fpstorepickup')->setAccountType(trim($request->getPost('fermopoint_account')));
        Mage::helper('fpstorepickup')->setPhoneNumber(trim($request->getPost('fermopoint_phone')));
        
        $point = Mage::getSingleton('fpstorepickup/points')->getPoint($pointId);
        if ($point->getId()) 
        {
            $billingAddress = $quote->getBillingAddress();
            $address = $quote->getShippingAddress();
            $address->addData($point->getAddressData());
            $address->setLastname('c/o ' . $address->getLastname() . ' - ' . $address->getFirstname());
            $address->setFirstname(Mage::helper('fpstorepickup')->getNickname());
            $address->setTelephone($billingAddress->getTelephone());
            
            $address->implodeStreetAddress();
            $address->setCollectShippingRates(true);
            
            $quote->collectTotals()->save();
        }
	}	
	
	public function onSaveOrderAfter($event)
	{
		$order = $event->getOrder();
        $orderPoint = Mage::getModel('fpstorepickup/order_point')->load($order->getId(), 'order_id');
        if ($orderPoint->getId())
            return;
        
        $shippingMethod = $order->getShippingMethod();
        if ($shippingMethod !== 'fpstorepickup_fpstorepickup')
            return;
            
        $pointId = Mage::helper('fpstorepickup')->getPointId();
        if ( ! $pointId)
            return;

        $options = array(
            'account_type' => Mage::helper('fpstorepickup')->getAccountType(),
            'nickname' => Mage::helper('fpstorepickup')->getNickname(),
            'phone_number' => Mage::helper('fpstorepickup')->getPhoneNumber(),
            'email' => $order->getCustomerEmail(),
        );
        $point = Mage::getSingleton('fpstorepickup/points')->getPoint($pointId);
        $orderPoint = Mage::getModel('fpstorepickup/order_point')
            ->createFrom($order, $point, $options)
        ;
        try {
            $orderPoint
                ->setTicketId(Mage::getSingleton('fpstorepickup/api')->submitOrder($order, $orderPoint))
                ->save()
            ;
        } catch (FermoPoint_StorePickup_Exception $e) {
            Mage::logException($e);
            return;
        }
        
        Mage::helper('fpstorepickup')->setUseMethod(false);
	}
    
    protected function _insertRadioJs(Mage_Core_Block_Abstract $block, Varien_Object $transport)
    {
        $html = $transport->getHtml();
        if ( ! preg_match('#(<ul[^>]+>.+?use_for_shipping.+?)</ul>#ius', $html, $matches))
            return;
            
        $html = str_replace(
            $matches[1], 
            $matches[1] . $block->getLayout()->createBlock('fpstorepickup/checkout_billing_radio')->toHtml(), 
            $html
        );
        $html .= $block->getLayout()->createBlock('fpstorepickup/checkout_billing_js')->toHtml();
        $transport->setHtml($html);
    }
    
    public function onBlockToHtmlAfter($event)
    {
        if ( ! Mage::getStoreConfig('carriers/fpstorepickup/active') 
            || ! Mage::getStoreConfig('carriers/fpstorepickup/accept')
        )
            return;
    
        $block = $event->getBlock();
        if ( ! $block)
            return;
            
        if ($block->getType() == 'checkout/onepage_billing')
            $this->_insertRadioJs($block, $event->getTransport());
    }
    
    public function onOrderInvoicePay($event)
    {
        $invoice = $event->getInvoice();
        $order = $invoice->getOrder();
        
        $orderId = $order->getId();
        $orderPoint = Mage::getModel('fpstorepickup/order_point')->load($orderId, 'order_id');
        if ( ! $orderPoint->getId() || $orderPoint->getIsApproved() || ! $orderPoint->getTicketId())
            return;
        
        $ticketId = $orderPoint->getTicketId();
        try {
            Mage::getSingleton('fpstorepickup/api')->approveOrderByTicketId($ticketId);
        } catch (FermoPoint_StorePickup_Exception $e) {
            Mage::logException($e);
            return;
        }
            
        $orderPoint
            ->setIsApproved(true)
            ->save()
        ;
        
        if ( ! $order->canShip())
            return;
            
        try {
            $shipment = $order->prepareShipment();
            if ($shipment)
            {
                $shipment->register();
                $shipment->getOrder()->setIsInProcess(true);
            
                $track = Mage::getModel('sales/order_shipment_track')
                     ->setShipment($shipment)
                     ->setData('title', Mage::helper('fpstorepickup')->__('Fermo!Point'))
                     ->setData('number', $ticketId)
                     ->setData('carrier_code', 'fpstorepickup')
                     ->setData('order_id', $shipment->getData('order_id'))
                 ;
                 
                 $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($shipment)
                    ->addObject($shipment->getOrder())
                    ->addObject($track)
                    ->save()
                ;
            }
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
        }
    }
    
    public function onOrderCancel($event)
    {
        $order = $event->getOrder();
        
        $orderId = $order->getId();
        $orderPoint = Mage::getModel('fpstorepickup/order_point')->load($orderId, 'order_id');
        if ( ! $orderPoint->getId() || $orderPoint->getIsCancelled() || ! $orderPoint->getTicketId())
            return;
        
        $ticketId = $orderPoint->getTicketId();
        try {
            Mage::getSingleton('fpstorepickup/api')->cancelOrderByTicketId($ticketId);
        } catch (FermoPoint_StorePickup_Exception $e) {
            Mage::logException($e);
            return;
        }
            
        $orderPoint
            ->setIsCancelled(true)
            ->save()
        ;
    }

}