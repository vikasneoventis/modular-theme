<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
namespace Amasty\Shopby\Model\Layer\Filter;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;

/**
 * Layer category filter
 */
class Stock extends AbstractFilter
{
    const FILTER_IN_STOCK = 1;
    const FILTER_OUT_OF_STOCK = 2;

    protected $attributeCode = 'stock_status';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $attributeValue;

    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $data
        );
        $this->_requestVar = 'stock';
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $filter = (int) $request->getParam($this->getRequestVar(), -1);
        if(!in_array($filter, [self::FILTER_IN_STOCK, self::FILTER_OUT_OF_STOCK])) {
            return $this;
        }

        $this->attributeValue = $filter;

        $applyFilter = $filter == self::FILTER_OUT_OF_STOCK ? 0 : 1;

        $this->getLayer()->getProductCollection()->addFieldToFilter($this->attributeCode, $applyFilter);

        $name = $filter == self::FILTER_IN_STOCK ? __('In Stock') : __('Out of Stock');

        $this->getLayer()->getState()->addFilter($this->_createItem($name, $filter));
        return $this;
    }

    /**
     * Get filter name
     *
     * @return \Magento\Framework\Phrase
     */
    public function getName()
    {
        $label = $this->scopeConfig->getValue('amshopby/stock_filter/label', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $label;
    }

    public function getPosition()
    {
        $position = (int) $this->scopeConfig->getValue('amshopby/stock_filter/position', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $position;
    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $productCollectionOrigin = $this->getLayer()
            ->getProductCollection();

        if($this->attributeValue){
            $productCollection = clone $productCollectionOrigin;
            $requestBuilder = clone $productCollection->_memRequestBuilder;
            $requestBuilder->removePlaceholder($this->attributeCode);
            $productCollection->setRequestData($requestBuilder);
            $productCollection->clear();
            $productCollection->loadWithFilter();
            $collection = $productCollection;
        }else{
            $collection = $productCollectionOrigin;
        }
        $optionsFacetedData = $collection->getFacetedData($this->attributeCode);

        $in_stock = isset($optionsFacetedData[1]) ? $optionsFacetedData[1]['count'] : 0;
        $out_stock = isset($optionsFacetedData[0]) ? $optionsFacetedData[0]['count'] : 0;

        $listData = [
            [
                'label' => __('In Stock'),
                'value' => self:: FILTER_IN_STOCK,
                'count' => $in_stock,
            ],
            [
                'label' => __('Out of Stock'),
                'value' => self:: FILTER_OUT_OF_STOCK,
                'count' => $out_stock,
            ]
        ];
        foreach ($listData as $data) {
            if($data['count'] < 1) {
                continue;
            }
            $this->itemDataBuilder->addItemData(
                $data['label'],
                $data['value'],
                $data['count']
            );
        }

        return $this->itemDataBuilder->build();
    }
}
