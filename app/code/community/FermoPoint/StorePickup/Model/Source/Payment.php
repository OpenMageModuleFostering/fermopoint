<?php

class FermoPoint_StorePickup_Model_Source_Payment
{
    public function toOptionArray()
	{
        $website = Mage::getSingleton('adminhtml/config_data')->getWebsite();
        $website_id = Mage::getModel('core/website')->load($website)->getId();
        $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
		$collection = Mage::getModel('payment/config')->getActiveMethods($store_id);
		
		if ( ! count($collection))
			return;
			
		$options = array();	
		foreach ($collection as $item)
		{
			$title = $item->getTitle() ? $item->getTitle() : $item->getId();
			$options[] = array('value' => $item->getId(), 'label' => $title);
		}
		
		return $options;
	}
}
