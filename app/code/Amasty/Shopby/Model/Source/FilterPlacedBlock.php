<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

/**
 * Copyright © 2016 Amasty. All rights reserved.
 */

namespace Amasty\Shopby\Model\Source;


class FilterPlacedBlock implements \Magento\Framework\Option\ArrayInterface
{
    const POSITION_SIDEBAR = 1;
    const POSITION_TOP = 2;
    const POSITION_BOTH = 3;

    public function toOptionArray()
    {
        return [
            [
                'value' => self::POSITION_SIDEBAR,
                'label' => __('Sidebar')
            ],
            [
                'value' => self::POSITION_TOP,
                'label' => __('Top')
            ],
            /*[
                'value' => self::POSITION_BOTH,
                'label' => __('Both')
            ]*/
        ];
    }
}
