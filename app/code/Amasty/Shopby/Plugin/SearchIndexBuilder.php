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


class SearchIndexBuilder
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }


    public function afterBuild($subject, $result)
    {
        if($this->isEnabledShowOutOfStock() && $this->isEnabledStockFilter()) {
            $this->addStockDataToSelect($result);
        }

        if($this->isEnabledRatingFilter()) {
            $this->addRatingDataToSelect($result);
        }

        return $result;
    }

    protected function addStockDataToSelect($select)
    {
        $connection = $select->getConnection();

        $select->joinLeft(
            ['stock_index' => $connection->getTableName('cataloginventory_stock_status')],
            'search_index.entity_id = stock_index.product_id'
            . $connection->quoteInto(
                ' AND stock_index.website_id = ?',
                $this->storeManager->getWebsite()->getId()
            ),
            []
        );
    }

    protected function addRatingDataToSelect($select)
    {
        $connection = $select->getConnection();

        $select->joinLeft(
            ['rating' => $connection->getTableName('review_entity_summary')],
            sprintf('`rating`.`entity_pk_value`=`search_index`.entity_id
                AND `rating`.entity_type = 1
                AND `rating`.store_id  =  %d',
                $this->storeManager->getStore()->getId()),
            []
        );
    }

    protected function isEnabledShowOutOfStock()
    {
        return $this->scopeConfig->isSetFlag(
            'cataloginventory/options/show_out_of_stock',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    protected function isEnabledStockFilter()
    {
        return $this->scopeConfig->isSetFlag('amshopby/stock_filter/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    protected function isEnabledRatingFilter()
    {
        return $this->scopeConfig->isSetFlag('amshopby/rating_filter/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
