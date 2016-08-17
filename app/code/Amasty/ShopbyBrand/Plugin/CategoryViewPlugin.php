<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\ShopbyBrand\Plugin;


use Amasty\ShopbyBrand\Helper\Content;
use Magento\Catalog\Block\Category\View;
use Magento\Catalog\Model\Category;

class CategoryViewPlugin
{
    public function afterIsMixedMode(View $subject, $isMixedMode)
    {
        if (!$isMixedMode) {
            $category = $subject->getCurrentCategory();
            if ($category->getData('amshopby_brand_force_mixed_mode')) {
                $isMixedMode = true;
            }
        }

        return $isMixedMode;
    }
}
