<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Helper;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\App\Helper\Context;
use Amasty\Shopby;
use Amasty\Shopby\Model\ResourceModel\FilterSetting\Collection;
use Amasty\Shopby\Model\ResourceModel\FilterSetting\CollectionFactory;
use Amasty\Shopby\Api\Data\FilterSettingInterface;

class FilterSetting extends \Magento\Framework\App\Helper\AbstractHelper
{
    /** @var  Collection */
    protected $collection;

    /** @var  Shopby\Model\FilterSettingFactory */
    protected $settingFactory;

    public function __construct(Context $context, CollectionFactory $settingCollectionFactory, Shopby\Model\FilterSettingFactory $settingFactory)
    {
        parent::__construct($context);
        $this->collection = $settingCollectionFactory->create();
        $this->settingFactory = $settingFactory;
    }

    /**
     * @param FilterInterface $layerFilter
     * @return Shopby\Api\Data\FilterSettingInterface
     */
    public function getSettingByLayerFilter(FilterInterface $layerFilter)
    {
        $filterCode = $this->getFilterCode($layerFilter);
        $setting = null;
        if (isset($filterCode)) {
            $setting = $this->collection->getItemByColumnValue(Shopby\Model\FilterSetting::FILTER_CODE, $filterCode);
        }
        if (is_null($setting)) {
            $data = [FilterSettingInterface::FILTER_CODE=>$filterCode];
            if($layerFilter instanceof \Amasty\Shopby\Model\Layer\Filter\Stock) {
                $data = $this->getDataByCustomFilter('stock');
            } elseif($layerFilter instanceof \Amasty\Shopby\Model\Layer\Filter\Rating) {
                $data = $this->getDataByCustomFilter('rating');
            }
            $setting = $this->settingFactory->create(['data'=>$data]);
        }
        return $setting;
    }

    /**
     * @param $attributeModel
     *
     * @return Shopby\Model\FilterSetting|\Magento\Framework\DataObject
     */
    public function getSettingByAttribute($attributeModel)
    {
        $filterCode = 'attr_' . $attributeModel->getAttributeCode();
        $setting = $this->collection->getItemByColumnValue(Shopby\Model\FilterSetting::FILTER_CODE, $filterCode);
        if (is_null($setting)) {
            $setting = $this->settingFactory->create();
        }

        return $setting;
    }

    protected function getFilterCode(FilterInterface $layerFilter)
    {
        try
        {
            // Produces exception when attribute model missing
            $attribute = $layerFilter->getAttributeModel();
            return 'attr_' . $attribute->getAttributeCode();
        } catch (\Exception $exception)
        {
            // Put here cases for special filters like Category, Stock etc.
            ;
        }

        return null;
    }

    protected function getDataByCustomFilter($filterName)
    {
        $data = [];
        $data[FilterSettingInterface::FILTER_SETTING_ID] = $filterName;
        $data[FilterSettingInterface::DISPLAY_MODE] = $this->scopeConfig->getValue('amshopby/'.$filterName.'_filter/display_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $data[FilterSettingInterface::FILTER_CODE] = $filterName;
        $data[FilterSettingInterface::IS_EXPANDED] = $this->scopeConfig->getValue('amshopby/'.$filterName.'_filter/is_expanded', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $data[FilterSettingInterface::TOOLTIP] = $this->scopeConfig->getValue('amshopby/'.$filterName.'_filter/tooltip', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        return $data;
    }
}
