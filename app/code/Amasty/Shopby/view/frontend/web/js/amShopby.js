/**
 * @author    Amasty Team
 * @copyright Copyright (c) Amasty Ltd. ( http://www.amasty.com/ )
 * @package   Amasty_Shopby
 */
define([
    "jquery",
    "jquery/ui",
    "mage/tooltip"
], function ($) {
    'use strict';

    $.widget('mage.amShopbyFilterAbstract',{
        options: {
            isAjax: 0
        },
        apply: function(link){
            window.location = link;
        }
    });

    $.widget('mage.amShopbyFilterItemDefault', $.mage.amShopbyFilterAbstract, {
        options: {
        },
        _create: function () {
            var self = this;
            $(function(){

                var link = self.element;
                var parent = link.parents('.item');
                var checkbox = parent.find('input[type=checkbox]');

                var params = {
                    parent:parent,
                    checkbox:checkbox,
                    link:link
                };

                checkbox.bind('click',params,function(e){
                    var link = e.data.link;
                    self.apply(link.prop('href'));
                    e.stopPropagation();
                });

                link.bind('click',params,function(e){
                    var element = e.data.checkbox;
                    element.prop('checked', !element.prop('checked'));
                    self.apply(e.data.link.prop('href'));
                    e.stopPropagation();
                    e.preventDefault();
                });

                parent.bind('click',params,function(e){
                    var element = e.data.checkbox;
                    var link = e.data.link;
                    element.prop('checked', !element.prop('checked'));
                    self.apply(link.prop('href'));
                    e.stopPropagation();
                });
            })
        }
    });

    $.widget('mage.amShopbyFilterDropdown', $.mage.amShopbyFilterAbstract, {
        options: {
        },
        _create: function () {
            var self = this;
            $(function(){
                var $select = $(self.element[0]);
                $select.change(function() {
                    self.apply($select.val());
                });
            })
        }
    });

    $.widget('mage.amShopbyFilterSlider', $.mage.amShopbyFilterAbstract, {
        options: {
        },
        _create: function () {
            var self = this;
            $(function(){

                var elementID = self.element[0].id;
                $( "#" + elementID + "_display" ).html( self.renderLabel(self.options.from)+ " - " + self.renderLabel(self.options.to) );
                var $slider = $("#" + elementID + "_slider");
                $slider.slider({
                    step: self.options.step,
                    range: true,
                    min: self.options.min,
                    max: self.options.max,
                    values: [ self.options.from, (self.options.to)],
                    slide: function( event, ui ) {
                        $( "#" + elementID + "_display" ).html( self.renderLabel(ui.values[ 0 ]) + " - " + self.renderLabel(ui.values[ 1 ]) );
                    },
                    change: function( event, ui ) {
                        var linkHref = self.options.url.replace('amshopby_slider_from', ui.values[ 0 ]).replace('amshopby_slider_to', ui.values[1]);
                        self.apply(linkHref);
                    }
                });
            });
        },
        renderLabel:function(value) {
            return this.options.template.replace('{amount}', value);
        }
    });

    $.widget('mage.amShopbyFilterSearch',{
        options: {
            highlightTemplate: "",
            itemsSelector: ""
        },

        previousSearch: '',

        _create: function () {
            var self = this;
            var $items = $(this.options.itemsSelector + " .item");
            $(self.element).keyup(function(){
                self.search(this.value, $items);
            });
        },

        search: function(searchText, $items) {
            var self = this;

            searchText = searchText.toLowerCase();
            if (searchText == this.previousSearch) {
                return;
            }
            this.previousSearch = searchText;

            if (searchText != '') {
                $(this.element).trigger('search_active');
            }

            $items.each(function(key, li) {
                if (li.hasAttribute('data-label')) {
                    var val = li.getAttribute('data-label').toLowerCase();
                    if (!val || val.indexOf(searchText) > -1) {
                        if (searchText != '' && val.indexOf(searchText) > -1) {
                            self.hightlight(li, searchText);
                        } else {
                            self.unhightlight(li);
                        }
                        $(li).show();
                    }
                    else {
                        self.unhightlight(li);
                        $(li).hide();
                    }
                }
            });

            if (searchText == '') {
                $(this.element).trigger('search_inactive');
            }
        },
        hightlight: function (element, searchText) {
            this.unhightlight(element);
            var $a = $(element).find('a');
            var label = $(element).attr('data-label');
            var newLabel = label.replace(new RegExp(searchText,'gi'), this.options.highlightTemplate);
            $a.find('.label').html(newLabel);
        },
        unhightlight: function(element) {
            var $a = $(element).find('a');
            var label = $(element).attr('data-label');
            $a.find('.label').html(label);
        }
    });

    $.widget('mage.amShopbyFilterHideMoreOptions',{
        options: {
            numberUnfoldedOptions: 0,
            _hideCurrent: false,
            buttonSelector: ""
        },
        _create: function () {
            var self = this;

            if ($(this.element).find(".item").length <= this.options.numberUnfoldedOptions) {
                $(this.options.buttonSelector).parent().hide();
                return;
            }

            $(this.element).parents('.filter-options-content').on('search_active', function() {
                if (self.options._hideCurrent) {
                    self.toggle(self.options.buttonSelector);
                }
                $(self.options.buttonSelector).parent().hide();
            });

            $(this.element).parents('.filter-options-content').on('search_inactive', function() {
                if (!self.options._hideCurrent) {
                    self.toggle(self.options.buttonSelector);
                }
                $(self.options.buttonSelector).parent().show();
            });

            $(this.options.buttonSelector).click(function(){
                self.toggle(this);
                return false;
            });
            $(this.options.buttonSelector).parent().click(function(){
                self.toggle(self.options.buttonSelector);
            });

            // for hide in first load
            $(this.options.buttonSelector).click();
        },

        toggle: function(button){
            var $button = $(button);
            if(this.options._hideCurrent) {
                this.showAll();
                $button.html($button.attr('data-text-less'));
                $button.attr('data-is-hide', 'false');
                this.options._hideCurrent = false;
            } else {
                this.hideAll();
                $button.html($button.attr('data-text-more'));
                $button.attr('data-is-hide', 'true');
                this.options._hideCurrent = true;
            }
        },

        hideAll: function () {
            var self = this;
            var count = 0;
            $(this.element).find(".item").each(function(){
                count++;
                if(count > self.options.numberUnfoldedOptions) {
                    $(this).hide();
                }
            });
        },
        showAll: function () {
            $(this.element).find(".item").show();
        },
    });

    $.widget('mage.amShopbyFilterAddTooltip',{
        options: {
            content: "",
            tooltipTemplate: ""
        },
        _create: function () {
            var template = this.options.tooltipTemplate.replace('{content}', this.options.content);
            var $template = $(template);

            var $place =  $(this.element).parents('.filter-options-item').find('.filter-options-title');
            if($place.length == 0) {
                $place = $(this.element).parents('dd').prev('dt');
            }
            if($place.length > 0) {
                $place.append($template);
            }

            $template.tooltip({
                position: {
                    my: "left bottom-10",
                    at: "left top",
                    collision: "flipfit flip",
                    using: function( position, feedback ) {
                        $( this ).css( position );
                        $( "<div>" )
                            .addClass( "arrow" )
                            .addClass( feedback.vertical )
                            .addClass( feedback.horizontal )
                            .appendTo( this );
                    }
                },
            });
        }
    });
});
