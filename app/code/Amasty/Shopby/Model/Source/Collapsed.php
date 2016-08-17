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


class Collapsed implements \Magento\Framework\Option\ArrayInterface
{
    const COLLAPSED = 1;
    const EXPANDED = 0;

    public function toOptionArray()
    {
        return [
            [
                'value' => self::COLLAPSED,
                'label' => __('Collapsed')
            ],
            [
                'value' => self::EXPANDED,
                'label' => __('Expanded')
            ],
            /*[
                'value' => self::POSITION_BOTH,
                'label' => __('Both')
            ]*/
        ];
    }
}
