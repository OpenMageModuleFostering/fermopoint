<?php

class FermoPoint_StorePickup_Block_Checkout_Billing_Radio extends Mage_Core_Block_Template {

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('fpstorepickup/checkout/onepage/billing/radio.phtml');
    }

}
