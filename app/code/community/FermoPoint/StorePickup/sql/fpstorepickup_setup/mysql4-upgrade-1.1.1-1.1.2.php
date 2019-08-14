<?php

$installer = $this;

$installer->startSetup();

$installer->run("
    ALTER TABLE `{$installer->getTable('fpstorepickup/order_point')}`
        ADD COLUMN `dob` DATE NULL DEFAULT NULL AFTER nickname
    ;
");

$installer->endSetup(); 
