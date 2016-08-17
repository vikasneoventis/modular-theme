<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\ShopbyBrand\Plugin;

use Magento\Catalog\Block\Category\View;
use Magento\Catalog\Model\Category;

class CategoryPlugin
{
    /**
     * @param Category $subject
     * @param string|null $result
     * @return string|null
     */
    public function afterGetImageUrl(Category $subject, $result)
    {
        if ($subject->hasData('amshopby_brand_image_url')) {
            return $subject->getData('amshopby_brand_image_url');
        } else {
            return $result;
        }
    }
}
