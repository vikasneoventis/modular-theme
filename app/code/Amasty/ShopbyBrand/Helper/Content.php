<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\ShopbyBrand\Helper;

use Amasty\Shopby\Api\Data\OptionSettingInterface;
use Amasty\Shopby\Helper\OptionSetting;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\View\Page\Config;
use Magento\Store\Model\StoreManager;


class Content extends AbstractHelper
{
    /** @var  Layer */
    protected $layer;

    /** @var  OptionSetting */
    protected $optionHelper;

    /** @var  StoreManager */
    protected $storeManager;

    public function __construct(Context $context, Layer\Resolver $layerResolver, OptionSetting $optionHelper, StoreManager $storeManager)
    {
        parent::__construct($context);
        $this->layer = $layerResolver->get();
        $this->optionHelper = $optionHelper;
        $this->storeManager = $storeManager;
    }

    public function setCategoryData(Category $category)
    {
        $brand = $this->getCurrentBranding();
        if ($brand) {
            $this->populateCategoryWithBrand($category, $brand);
        }
    }

    /**
     * @return null|OptionSettingInterface
     */
    public function getCurrentBranding()
    {
        $attribute_code = $this->scopeConfig->getValue('amshopby_brand/general/attribute_code');
        if ($attribute_code == '') {
            return null;
        }

        $value = $this->_getRequest()->getParam($attribute_code);
        if (!isset($value)) {
            return null;
        }

        $isRootCategory = $this->layer->getCurrentCategory()->getId() == $this->layer->getCurrentStore()->getRootCategoryId();
        if (!$isRootCategory) {
            return null;
        }

        $setting = $this->optionHelper->getSettingByValue($value, 'attr_' . $attribute_code, $this->storeManager->getStore()->getId());

        return $setting;
    }

    protected function populateCategoryWithBrand(Category $category, OptionSettingInterface $brand)
    {
        $category->setName($brand->getTitle());
        $category->setData('description', $brand->getDescription());
        $category->setData('landing_page', $brand->getTopCmsBlockId());
        if ($brand->getTopCmsBlockId()) {
            $category->setData('amshopby_brand_force_mixed_mode', 1);
        }
        $category->setData('amshopby_brand_image_url', $brand->getImageUrl());

        $category->setData('meta_title', $brand->getMetaTitle());
        $category->setData('meta_description', $brand->getMetaDescription());
        $category->setData('meta_keywords', $brand->getMetaKeywords());

    }
}
