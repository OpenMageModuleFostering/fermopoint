<?php

class FermoPoint_StorePickup_Block_FireCheckout_Billing_Js extends Mage_Core_Block_Template {

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('fpstorepickup/firecheckout/onepage/billing/js.phtml');
    }

}
