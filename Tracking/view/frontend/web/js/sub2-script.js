define(['jquery'], function ($) {
    'use strict';
    $.widget('mage.sub2Script', {
        options: {
            bindSubmit: true,
            customerEmail: '',
            orderDetails: ''
        },

        _create: function () {
            if (this.options.bindSubmit) {
                this._bindSubmit();
            }
        },

        _bindSubmit: function () {
            const self = this;

            // order track
            if (window.location.pathname.includes("checkout/onepage/success")) {
                this.orderTrack();
            }

            // email capture track
            if ($('#sub2-script').data('is-emailcapture-enabled')) {
                //customer acc creation
                $('.form-create-account').find('.action.submit.primary').on('click', function (e) {
                    self.emailCapture(e, '.form-create-account', 'input[name="email"]');
                });

                //customer login
                $('.form-login').find('.action.login.primary').on('click', function (e) {
                    self.emailCapture(e, '.form-login', '#email');
                });

                //newsletter subscription page
                $('.form-newsletter-manage').find('.action.save.primary').on('click', function (e) {
                    self.emailCapture(e, '.form-newsletter-manage', '#subscription');
                });

                //newsletter on footer
                $('#newsletter-validate-detail').find('.action.subscribe.primary').on('click', function (e) {
                    self.emailCapture(e, '#newsletter-validate-detail', 'input[name="email"]');
                });

                //contact us form
                $('#contact-form').find('.action.submit.primary').on('click', function (e) {
                    self.emailCapture(e, '#contact-form', 'input[name="email"]');
                });

                //checkout sign-in popup
                this.elementExists('.checkout-index-index [data-role="content"]', function () {
                    $('[data-role="login"]').find('.action.action-login.secondary').on('click', function (e) {
                        self.emailCapture(e, '[data-role="login"]', '#login-email');
                    });
                }, 100);

                //checkout page sign-in
                this.elementExists('.checkout-index-index .form-login', function () {
                    $('.form-login').find('.action.login.primary').on('click', function (e) {
                        self.emailCapture(e, '.form-login', '#customer-email');
                    });
                }, 100);
            }

        },

        elementExists: function (selector, myFunction, intervalTime) {
            const interval = setInterval(function () {
                if ($(selector).length > 0) {
                    myFunction();
                    clearInterval(interval);
                }
            }, intervalTime);
        },

        emailCapture: function (e, formSelector, emailSelector) {

            var optIn = 1, customerEmailId = '';
            if (formSelector == '.form-newsletter-manage') {
                customerEmailId = this.options.customerEmail;
                optIn = ($(emailSelector).is(':checked')) ? 1 : 0;
            } else {
                customerEmailId = $(formSelector).find(emailSelector).val();
            }

            if ((typeof __s2tQ != 'undefined' && __s2tQ instanceof Array) === true &&
                customerEmailId.length > 0 && customerEmailId != '' && $(formSelector).valid()) {
                e.preventDefault();
                __s2tQ.push(['storeData', {'Email': customerEmailId, 'OptIn': optIn}]);
                $(formSelector).submit();
            }
        },

        orderTrack: function () {

            var order = this.options.orderDetails;

            if ((typeof __s2tQ != 'undefined' && __s2tQ instanceof Array) === true &&
                order.hasOwnProperty("order_id")) {

                __s2tQ.push(['addOrder', {
                    'OrderID': order['order_id'],
                    'Affiliation': order['coupon_code'],
                    'Total': order['total'],
                    'Currency': order['currency_code'],
                    'Postcode': order['post_code']
                }]);

                $.each(order['items'], function (key, value) {
                    __s2tQ.push(['addItem', {
                        'OrderID': order['order_id'],
                        'Product_ID': value['product_id'],
                        'SKU': value['sku'],
                        'Unit_Price': value['price'],
                        'Quantity': value['qty'],
                        'Product_Name': value['name']
                    }]);
                });
            }
        }
    });
    return $.mage.sub2Script;
});
