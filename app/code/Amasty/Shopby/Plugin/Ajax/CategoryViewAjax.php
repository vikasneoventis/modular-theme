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


class CategoryViewAjax extends Ajax
{

    /**
     * @param \Magento\Catalog\Controller\Category\View $controller
     * @param                                           $page
     *
     * @return \Magento\Framework\Controller\Result\Raw|\Magento\Framework\View\Result\Page
     */
    public function afterExecute(\Magento\Catalog\Controller\Category\View $controller,  $page)
    {
        if(!$this->isAjax($controller) || !$page instanceof \Magento\Framework\View\Result\Page )
        {
            return $page;
        }

        $responseData = $this->getAjaxResponseData($page);
        $response = $this->prepareResponse($responseData);
        return $response;

    }
}
