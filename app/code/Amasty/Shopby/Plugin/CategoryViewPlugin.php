<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Plugin;

/**
 * Class CategoryViewPlugin
 *
 * @author Artem Brunevski
 */

use Magento\Catalog\Block\Category\View;
use Magento\Catalog\Model\Category;
use Amasty\Shopby\Model\Customizer\CategoryFactory as CustomizerCategoryFactory;

class CategoryViewPlugin
{
    /** @var CustomizerCategoryFactory  */
    protected $_customizerCategoryFactory;

    /** @var bool */
    protected $_categoryModified = false;

    /**
     * @param CustomizerCategoryFactory $customizerCategoryFactory
     */
    public function __construct(
        CustomizerCategoryFactory $customizerCategoryFactory
    ){
        $this->_customizerCategoryFactory = $customizerCategoryFactory;

    }

    /**
     * @param View $subject
     * @param Category $category
     * @return Category
     */
    public function afterGetCurrentCategory(View $subject, $category)
    {
        if ($category instanceof Category && !$this->_categoryModified) {
            $this->_customizerCategoryFactory->create()
                ->prepareData($category);

            $this->_categoryModified = true;
        }
        return $category;
    }
}