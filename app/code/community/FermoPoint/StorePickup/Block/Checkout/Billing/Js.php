<?php

class FermoPoint_StorePickup_Block_Checkout_Billing_Js extends Mage_Core_Block_Template {

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('fpstorepickup/checkout/onepage/billing/js.phtml');
    }

}
