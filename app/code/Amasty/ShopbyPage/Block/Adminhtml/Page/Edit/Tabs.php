<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\ShopbyPage\Block\Adminhtml\Page\Edit;

/**
 * Admin page left menu
 * * @author Artem Brunevski
 */

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('amasty_shopbypage_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Page Information'));
    }
}