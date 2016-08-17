<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

/**
 * Copyright © 2016 Amasty. All rights reserved.
 */

namespace Amasty\Shopby\Plugin\Ajax;


class Ajax
{
    /**
     * @var \Amasty\Shopby\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * CategoryViewAjax constructor.
     *
     * @param \Amasty\Shopby\Helper\Data                      $helper
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Amasty\Shopby\Helper\Data $helper,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        $this->helper = $helper;
        $this->resultRawFactory = $resultRawFactory;
    }

    /**
     * @param $controller
     *
     * @return bool
     */
    protected function isAjax($controller)
    {
        $isAjax = $controller->getRequest()->isAjax();
        return $this->helper->isAjaxEnabled() && $isAjax;
    }

    /**
     * @param \Magento\Framework\View\Result\Page $page
     *
     * @return array
     */
    protected function getAjaxResponseData(\Magento\Framework\View\Result\Page $page)
    {
        $products = $page->getLayout()->getBlock('category.products');
        $navigation = $page->getLayout()->getBlock('catalog.leftnav');
        $h1 = $page->getLayout()->getBlock('page.main.title');
        $title = $page->getConfig()->getTitle();

        $htmlCategoryData = '';
        $children = $page->getLayout()->getChildNames('category.view.container');
        foreach ($children as $child) {
            $htmlCategoryData .= $page->getLayout()->renderElement($child);
        }
        $htmlCategoryData = '<div class="category-view">' . $htmlCategoryData . '</div>';

        $shopbyCollapse = $page->getLayout()->getBlock('catalog.navigation.collapsing');
        $shopbyCollapseHtml = '';
        if($shopbyCollapse) {
            $shopbyCollapseHtml = $shopbyCollapse->toHtml();
        }

        $responseData = [
            'categoryProducts'=>$products->toHtml(),
            'navigation' => $navigation->toHtml().$shopbyCollapseHtml,
            'h1' => $h1->toHtml(),
            'title' => $title->get(),
            'categoryData' => $htmlCategoryData
        ];

        return $responseData;
    }

    /**
     * @param array $data
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    protected function prepareResponse(array $data)
    {
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($data));
        return $response;
    }
}
