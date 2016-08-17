/**
 * @author    Amasty Team
 * @copyright Copyright (c) Amasty Ltd. ( http://www.amasty.com/ )
 * @package   Amasty_Shopby
 */
define([
    "jquery",
    "jquery/ui",
    "Amasty_Shopby/js/amShopby",
    "productListToolbarForm"
], function ($) {
    'use strict';
    $.widget('mage.amShopbyAjax',{
        options:{
            _isAmshopbyAjaxProcessed: false
        },
        _create: function (){
            var self = this;
            $(function(){
                self.initAjax();
                if (typeof window.history.replaceState === "function") {
                    window.history.replaceState({url: document.URL}, document.title);

                    setTimeout(function() {
                        /*
                         Timeout is a workaround for iPhone
                         Reproduce scenario is following:
                         1. Open category
                         2. Use pagination
                         3. Click on product
                         4. Press "Back"
                         Result: Ajax loads the same content right after regular page load
                         */
                        window.onpopstate = function(e){
                            if(e.state){
                                self.updateContent(e.state.url, false);
                            }
                        };
                    }, 0)
                }
            });

        },

        updateContent: function(link, isPushState){
            var self = this;
            $("#amasty-shopby-overlay").show();
            if (typeof window.history.pushState === 'function' && isPushState) {
                window.history.pushState({url: link}, '', link);
            }
            $.getJSON(link, {isAjax: 1}, function(data){
                $('.block.filter').first().replaceWith(data.navigation);
                $('.block.filter').first().trigger('contentUpdated');
                $('#amasty-shopby-product-list').replaceWith(data.categoryProducts);
                $('#amasty-shopby-product-list').trigger('contentUpdated');

                $('#page-title-heading').parent().replaceWith(data.h1);
                $('#page-title-heading').trigger('contentUpdated');

                $('title').html(data.title);
                if(data.categoryData != '') {
                    if($(".category-view").length == 0) {
                        $('<div class="category-view"></div>').insertAfter('.page.messages');
                    }
                    $(".category-view").replaceWith(data.categoryData);
                }



                $("#amasty-shopby-overlay").hide();
                self.initAjax();
            });
        },

        initAjax: function()
        {
            var self = this;
            $.mage.amShopbyFilterAbstract.prototype.apply = function(link){
                self.updateContent(link, true);
            }
            this.options._isAmshopbyAjaxProcessed = false;
            $.mage.productListToolbarForm.prototype.changeUrl = function (paramName, paramValue, defaultValue) {
                if(self.options._isAmshopbyAjaxProcessed) {
                    return;
                }
                self.options._isAmshopbyAjaxProcessed = true;
                var urlPaths = this.options.url.split('?'),
                    baseUrl = urlPaths[0],
                    urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                    paramData = {},
                    parameters;
                for (var i = 0; i < urlParams.length; i++) {
                    parameters = urlParams[i].split('=');
                    paramData[parameters[0]] = parameters[1] !== undefined
                        ? window.decodeURIComponent(parameters[1].replace(/\+/g, '%20'))
                        : '';
                }
                paramData[paramName] = paramValue;
                if (paramValue == defaultValue) {
                    delete paramData[paramName];
                }
                paramData = $.param(paramData);

                //location.href = baseUrl + (paramData.length ? '?' + paramData : '');
                self.updateContent(baseUrl + (paramData.length ? '?' + paramData : ''), true);
            }
            var changeFunction = function(e){
                self.updateContent($(this).prop('href'), true);
                e.stopPropagation();
                e.preventDefault();
            };
            $(".swatch-option-link-layered").bind('click', changeFunction);
            $(".filter-current a").bind('click',changeFunction);
            $(".filter-actions a").bind('click', changeFunction);
            $(".toolbar .pages a").bind('click', changeFunction);
        }
    });

});
