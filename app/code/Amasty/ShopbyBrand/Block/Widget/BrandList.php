<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\ShopbyBrand\Block\Widget;

use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Url;

class BrandList extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /** @var  Repository */
    protected $repository;

    /** @var  Url */
    protected $url;

    /** @var  ScopeConfigInterface */
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Repository $repository,
        Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $context->getScopeConfig();
        $this->repository = $repository;
        $this->url = $url;
    }

    public function getIndex()
    {
        $items = $this->getItems();
        if (is_null($items)) {
            return null;
        }

        $this->sortItems($items);

        $letters = $this->items2letters($items);

        $columnCount = abs(intval($this->getData('columns')));
        if (!$columnCount) {
            $columnCount = 1;
        }
        $itemsPerColumn = ceil((sizeof($items) + sizeof($letters)) / max(1, $columnCount));

        $col = 0; // current column
        $num = 0; // current number of items in column
        $index = [];
        foreach ($letters as $letter => $items){
            $index[$col][$letter] = $items['items'];
            $num += $items['count'];
            $num++;
            if ($num >= $itemsPerColumn){
                $num = 0;
                $col++;
            }
        }

        return $index;
    }

    public function getItems()
    {
        $attribute_code = $this->scopeConfig->getValue('amshopby_brand/general/attribute_code');
        if ($attribute_code == '') {
            return null;
        }

        $options = $this->repository->get($attribute_code)->getOptions();
        array_shift($options);

        $items = [];
        foreach ($options as $option) {
            $itemUrl = $this->url->getUrl('amshopby/index/index', [
                '_query' => [$attribute_code => $option->getValue()],
            ]);

            $items[] = [
                'label' => $option->getLabel(),
                'url' => $itemUrl,
                'img' => null,
            ];
        }

        return $items;
    }

    protected function sortItems(array &$items)
    {
        usort($items, function($a, $b) {
            $a['label'] = trim($a['label']);
            $b['label'] = trim($b['label']);

            $x = substr($a['label'], 0, 1);
            $y = substr($b['label'], 0, 1);
            if (is_numeric($x) && !is_numeric($y)) return 1;
            if (!is_numeric($x) && is_numeric($y)) return -1;

            return strcmp(strtoupper($a['label']), strtoupper($b['label']));
        });
    }

    protected function items2letters($items)
    {
        $letters = [];
        foreach ($items as $item) {
            if (function_exists('mb_strtoupper')) {
                $i = mb_strtoupper(mb_substr($item['label'], 0, 1, 'UTF-8'));
            } else {
                $i = strtoupper(substr($item['label'], 0, 1));
            }

            if (is_numeric($i)) { $i = '#'; }

            if (!isset($letters[$i]['items'])){
                $letters[$i]['items'] = array();
            }

            $letters[$i]['items'][] = $item;

            if (!isset($letters[$i]['count'])){
                $letters[$i]['count'] = 0;
            }

            $letters[$i]['count']++;
        }

        return $letters;
    }
}
