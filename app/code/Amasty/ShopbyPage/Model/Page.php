<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\ShopbyPage\Model;

use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class Page
 *
 * @author Artem Brunevski
 */

class Page extends AbstractExtensibleModel
{
    /**
     * Position of placing meta data in category
     */
    const POSITION_REPLACE = 'replace';
    const POSITION_AFTER = 'after';
    const POSITION_BEFORE = 'before';

    const CATEGORY_FORCE_MIXED_MODE = 'amshopby_page_force_mixed_mode';
    const CATEGORY_FORCE_USE_CANONICAL = 'amshopby_page_force_use_canonical';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Amasty\ShopbyPage\Model\ResourceModel\Page');
    }

    /**
     * @return array|mixed
     */
    public function getConditionsUnserialized()
    {
        try {
            $ret = unserialize($this->getConditions());
        } catch (\Exception $e) {
            $ret = [];
        }
        if (!is_array($ret)){
            $ret = [];
        }
        
        $ret = array_values($ret); //rewrite array keys to ordinal

        return $ret;
    }

    /**
     * @return mixed
     */
    public function saveStores()
    {
        return $this->getResource()->saveStores($this);
    }
}
