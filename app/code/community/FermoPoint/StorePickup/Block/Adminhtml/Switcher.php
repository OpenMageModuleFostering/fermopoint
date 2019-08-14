<?php

class FermoPoint_StorePickup_Block_Adminhtml_Switcher extends Mage_Adminhtml_Block_Template
{
    /**
     * Get websites
     *
     * @return array
     */
    public function getWebsites()
    {
        $websites = Mage::app()->getWebsites();
        $clientIds = array();
        $config = Mage::helper('fpstorepickup/config');
        foreach ($websites as $websiteId => $website) {
            $store_id = Mage::app()->getWebsite($website)->getDefaultStore()->getId();
            if ( Mage::getStoreConfig('carriers/fpstorepickup/active', $store_id)
                && Mage::getStoreConfig('carriers/fpstorepickup/accept', $store_id)
            ) {
                $clientIds[Mage::getStoreConfig($config::XML_PATH_CLIENTID, $store_id)][] = array('id' => $websiteId, 'name' => $website->getName());
            }
        }

        $result = array();
        foreach ($clientIds as $clientId => $webistes) {
            $first_website = array_shift($webistes);
            $key = $first_website['id'];
            $value = $first_website['name'];
            $result[$key] = $value;
            foreach($webistes as $website) {
                $result[$key] .= ' / '.$website["name"];
            }
        }
        return $result;
    }

    public function getSwitchUrl()
    {
        return $this->getUrl('*/*/*', array('_current' => true, 'website' => null));
    }

    public function getWebsiteId()
    {
        $website = $this->getRequest()->getParam('website', null);
        if (!$website) {
            $websites = $this->getWebsites();
            $website = key($websites);
            Mage::app()->getRequest()->setPost('website', $website);
        }

        return $website;
    }

}
