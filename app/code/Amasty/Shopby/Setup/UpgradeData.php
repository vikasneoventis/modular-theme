<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

/**
 * Copyright © 2016 Amasty. All rights reserved.
 */

namespace Amasty\Shopby\Setup;


use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    protected $deployHelper;

    public function __construct(
        \Amasty\Base\Helper\Deploy $deployHelper
    ) {
        $this->deployHelper = $deployHelper;
    }
    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @return void
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {

        if (version_compare($context->getVersion(), '1.6.3', '<')) {
            $this->deployPub();
        }
    }

    protected function deployPub()
    {
        $p = strrpos(__DIR__, '/');
        $modulePath = $p ? substr(__DIR__, 0, $p) : __DIR__;
        $modulePath .= '/pub';
        $this->deployHelper->deployFolder($modulePath);
    }
}
