define([
    'ko',
    'uiComponent',
    'jquery',
    'mage/url',
    'Magento_Customer/js/customer-data'
], function (ko, Component, $, url, customerData) {
    'use strict';

    return Component.extend({

        initialize: function () {
            this._super();
            var self = this;
            ko.computed(function () {
                return ko.toJSON(customerData.get('cart')().subtotal);
            }).subscribe(function () {
                if ($('#sub2-script').data('is-addtocart-enabled')) {
                    self.getData();
                }
            });
        },

        getData: function () {
            var cartItems = [], currency = window.getCurrency, products = customerData.get('cart')().items;
            if ((typeof __s2tQ != 'undefined' && __s2tQ instanceof Array) === true && products.length > 0) {
                $.each(products, function (key, value) {
                    cartItems.push({
                        'SKU': value['product_sku'],
                        'Product_ID': value['product_id'],
                        'Product_Name': value['product_name'],
                        'Unit_Price': value['product_price_value'],
                        'Currency': currency.getCurrencySymbol,
                        'Quantity': value['qty']
                    });
                });
                __s2tQ.push(['sendBasketJS', cartItems]);
            }
        }
    });
});
