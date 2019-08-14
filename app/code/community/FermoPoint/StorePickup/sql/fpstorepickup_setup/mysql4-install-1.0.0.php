<?php

$installer = $this;

$installer->startSetup();

if (!$installer->tableExists($installer->getTable('fpstorepickup/point'))) {
    $installer->run("
    
        CREATE TABLE IF NOT EXISTS `{$installer->getTable('fpstorepickup/point')}` (
          `point_id` int(10) unsigned NOT NULL COMMENT 'Point ID',
          `name` text NOT NULL COMMENT 'Point Name',
          `address` text NOT NULL COMMENT 'Point Address',
          `point_data` text NOT NULL COMMENT 'Serialized Point Data',
          PRIMARY KEY (`point_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Fermopoint Storepickup Point';

    ");
}

if (!$installer->tableExists($installer->getTable('fpstorepickup/order_point'))) {
    $installer->run("

        CREATE TABLE IF NOT EXISTS `{$installer->getTable('fpstorepickup/order_point')}` (
          `order_point_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Order Point ID',
          `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Order ID',
          `point_id` int(10) unsigned NULL DEFAULT NULL COMMENT 'Point ID',
          `ticket_id` varchar(255) NOT NULL COMMENT 'Ticket ID',
          `is_approved` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is Approved Flag',
          `is_cancelled` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is Cancelled Flag',
          `account_type` varchar(255) NOT NULL COMMENT 'Account Type',
          `nickname` varchar(255) NOT NULL COMMENT 'Nickname',
          `email` varchar(255) NOT NULL COMMENT 'Email',
          `phone_number` varchar(255) NOT NULL COMMENT 'Phone Number',
          `point_name` text NOT NULL COMMENT 'Point Name',
          `point_address` text NOT NULL COMMENT 'Point Address',
          `point_data` text NOT NULL COMMENT 'Serialized Point Data',
          PRIMARY KEY (`order_point_id`),
          KEY `IDX_FPSP_ORDER_POINT_ORDER_ID` (`order_id`),
          KEY `IDX_FPSP_ORDER_POINT_POINT_ID` (`point_id`),
          CONSTRAINT `FK_FPSP_ORDER_POINT_POINT_ID_FPSP_POINT_POINT_ID` FOREIGN KEY (`point_id`) REFERENCES {$installer->getTable('fpstorepickup/point')} (`point_id`) ON DELETE SET NULL ON UPDATE CASCADE,
          CONSTRAINT `FK_FPSP_ORDER_POINT_ORDER_ID_SALES_FLAT_ORDER_ENTITY_ID` FOREIGN KEY (`order_id`) REFERENCES {$installer->getTable('sales/order')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Fermopoint Storepickup Order to Point';

    ");

}

$installer->endSetup(); 
