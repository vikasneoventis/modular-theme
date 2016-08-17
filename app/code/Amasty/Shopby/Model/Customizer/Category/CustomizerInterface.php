<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Model\Customizer\Category;

/**
 * Interface CustomizerInterface
 *
 * @author Artem Brunevski
 */

use Magento\Catalog\Model\Category;

interface CustomizerInterface
{
    public function prepareData(Category $category);
}