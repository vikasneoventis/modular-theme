<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\ShopbyBrand\Plugin;

use Amasty\Shopby\Model\Layer\Filter\Attribute;
use Amasty\ShopbyBrand\Helper\Content;

class AttributeFilterPlugin
{
    /** @var  Content */
    protected $contentHelper;

    public function __construct(Content $contentHelper)
    {
        $this->contentHelper = $contentHelper;
    }

    /**
     * @param Attribute $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsVisibleWhenSelected(Attribute $subject, $result)
    {
        if ($result) {
            if ($this->isBrandingBrand($subject)) {
                $result = false;
            }
        }

        return $result;
    }

    public function afterShouldAddState(Attribute $subject, $result)
    {
        if ($result) {
            if ($this->isBrandingBrand($subject)) {
                $result = false;
            }
        }

        return $result;
    }

    protected function isBrandingBrand(Attribute $subject)
    {
        $brand = $this->contentHelper->getCurrentBranding();
        return $brand && ('attr_' . $subject->getRequestVar() == $brand->getFilterCode());
    }
}
