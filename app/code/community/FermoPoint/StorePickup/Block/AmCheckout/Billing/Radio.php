<?php

class FermoPoint_StorePickup_Block_AmCheckout_Billing_Radio extends Mage_Core_Block_Template {

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('fpstorepickup/amcheckout/onepage/billing/radio.phtml');
    }

}
