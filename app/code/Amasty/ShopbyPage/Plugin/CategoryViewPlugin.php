<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\ShopbyPage\Plugin;

/**
 * Class CategoryViewPlugin
 *
 * @author Artem Brunevski
 */

use Magento\Catalog\Block\Category\View;
use Magento\Catalog\Model\Category;
use Amasty\ShopbyPage\Model\Page;

class CategoryViewPlugin
{
    /**
     * @param View $subject
     * @param $isMixedMode
     * @return bool
     */
    public function afterIsMixedMode(View $subject, $isMixedMode)
    {
        if (!$isMixedMode) {
            $category = $subject->getCurrentCategory();
            if ($category->getData(Page::CATEGORY_FORCE_MIXED_MODE)) {
                $isMixedMode = true;
            }
        }
        return $isMixedMode;
    }
}