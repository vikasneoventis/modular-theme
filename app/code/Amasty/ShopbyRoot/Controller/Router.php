<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\ShopbyRoot\Controller;

use Amasty\ShopbySeo\Helper\UrlParser;
use Magento\Framework\Module\Manager;

class Router implements \Magento\Framework\App\RouterInterface
{
    /** @var \Magento\Framework\App\ActionFactory */
    protected $actionFactory;

    /** @var \Magento\Framework\App\ResponseInterface */
    protected $response;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /** @var  Manager */
    protected $moduleManager;

    /** @var  \Magento\Framework\Registry */
    protected $registry;

    /** @var  UrlParser */
    protected $urlParser;

    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $registry,
        UrlParser $urlParser,
        Manager $moduleManager)
    {
        $this->actionFactory = $actionFactory;
        $this->response = $response;
        $this->scopeConfig = $scopeConfig;
        $this->moduleManager = $moduleManager;
        $this->registry = $registry;
        $this->urlParser = $urlParser;
    }

    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $shopbyPageUrl = $this->scopeConfig->getValue('amshopby_root/general/url',  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $identifier = trim($request->getPathInfo(), '/');

        $brandUrlKeyMatched = false;
        if ($this->moduleManager->isEnabled('Amasty_ShopbyBrand')) {
            $urlKey = $this->scopeConfig->getValue('amshopby_brand/general/url_key');
            $brandUrlKeyMatched = $urlKey == $identifier;
        }

        if($identifier == $shopbyPageUrl || $brandUrlKeyMatched) {
            // Forward Shopby
            if ($this->isRouteAllowed($request)) {
                $request->setModuleName('amshopby')->setControllerName('index')->setActionName('index');
                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
                return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
            }
        }

        if ($this->moduleManager->isEnabled('Amasty_ShopbySeo') && $this->scopeConfig->getValue('amasty_shopby_seo/url/mode')) {
            $params = $this->urlParser->parseSeoPart($identifier);
            if ($params) {
                $this->registry->register('amasty_shopby_seo_parsed_params', $params);

                // Forward to very short brand-like url
                if ($this->isRouteAllowed($request)) {
                    $request->setModuleName('amshopby')->setControllerName('index')->setActionName('index');
                    $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $shopbyPageUrl);

                    $params = array_merge($params, $request->getParams());
                    $request->setParams($params);
                    return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
                }
            }
        }
    }

    protected function isRouteAllowed(\Magento\Framework\App\RequestInterface $request)
    {
        if ($this->scopeConfig->isSetFlag('amshopby_root/general/enabled')) {
            return true;
        }
        $attribute_code = $this->scopeConfig->getValue('amshopby_brand/general/attribute_code');
        if (!$attribute_code) {
            return false;
        }

        $seoParams = $this->registry->registry('amasty_shopby_seo_parsed_params');
        $seoBrandPresent = isset($seoParams) && array_key_exists($attribute_code, $seoParams);
        return $request->getParam($attribute_code) || $seoBrandPresent;
    }
}
