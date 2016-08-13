<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

/**
 * Copyright © 2016 Amasty. All rights reserved.
 */

namespace Amasty\Shopby\Plugin;


class FilterList
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * FilterList constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ){
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;

    }

    /**
     * @param \Magento\Catalog\Model\Layer\FilterList $subject
     * @param \Closure                                $closure
     * @param \Magento\Catalog\Model\Layer            $layer
     *
     * @return array
     */
    public function aroundGetFilters(\Magento\Catalog\Model\Layer\FilterList $subject, \Closure $closure, \Magento\Catalog\Model\Layer $layer)
    {
        $listFilters = $closure($layer);
        $listAdditionalFilters = $this->getAdditionalFilters($layer);
        return $this->insertAdditionalFilters($listFilters, $listAdditionalFilters);
    }

    /**
     * @param \Magento\Catalog\Model\Layer $layer
     *
     * @return array
     */
    protected function getAdditionalFilters(\Magento\Catalog\Model\Layer $layer)
    {
        if(is_null($this->filters)) {
            $this->filters = [];
            $isStockEnabled = $this->scopeConfig->isSetFlag('amshopby/stock_filter/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if($isStockEnabled && $this->isEnabledShowOutOfStock()) {
                $this->filters[] = $this->objectManager->create('Amasty\Shopby\Model\Layer\Filter\Stock', ['layer'=>$layer]);
            }
            $isRatingEnabled = $this->scopeConfig->isSetFlag('amshopby/rating_filter/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if($isRatingEnabled) {
                $this->filters[] = $this->objectManager->create('Amasty\Shopby\Model\Layer\Filter\Rating', ['layer'=>$layer]);
            }
        }

        return $this->filters;
    }

    protected function insertAdditionalFilters($listStandartFilters, $listAdditionalFilters)
    {
        if(count($listAdditionalFilters) == 0) {
            return $listStandartFilters;
        }
        $listNewFilters = [];
        foreach($listStandartFilters as $filter) {
            if(!$filter->hasAttributeModel()) {
                $listNewFilters[] = $filter;
                continue;
            }
            $position = $filter->getAttributeModel()->getPosition();
            foreach($listAdditionalFilters as $key=>$additionalFilter) {
                $additionalFilterPosition = $additionalFilter->getPosition();
                if($additionalFilterPosition <= $position) {
                    $listNewFilters[] = $additionalFilter;
                    unset($listAdditionalFilters[$key]);
                }
            }
            $listNewFilters[] = $filter;
        }
        $listNewFilters = array_merge($listNewFilters, $listAdditionalFilters);
        return $listNewFilters;
    }

    protected function isEnabledShowOutOfStock()
    {
        return $this->scopeConfig->isSetFlag(
            'cataloginventory/options/show_out_of_stock',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
