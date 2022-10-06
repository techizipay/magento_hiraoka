/**
 * Copyright © Lyra Network.
 * This file is part of Mi Cuenta Web plugin for Magento 2. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/*browser:true*/
/*global define*/
define(
    [
        'Lyranetwork_Micuentaweb/js/view/payment/method-renderer/micuentaweb-abstract',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Checkout/js/checkout-data'
    ],
    function(
        Component,
        selectPaymentMethodAction,
        checkoutData
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Lyranetwork_Micuentaweb/payment/micuentaweb-other',
                micuentawebOtherOption: window.checkoutConfig.payment.micuentaweb_other.availableOptions ?
                        window.checkoutConfig.payment.micuentaweb_other.availableOptions[0]['key'] : null,
            },

            initObservable: function() {
                this._super();
                this.observe('micuentawebOtherOption');

                return this;
            },

            getData: function() {
                var data = this._super();

                data['additional_data']['micuentaweb_other_option'] = this.micuentawebOtherOption();

                return data;
            },

            /**
             * Get payment method code
             */
            getOptionCode: function(option) {
                return this.getCode() + '_' + option;
            },

            /**
             * Get payment method data
             */
            getOptionData: function(method) {
                var data = this.getData();
                data['method'] =  method;

                return data;
            },

            selectOptionPaymentMethod: function(option) {
                var method = this.getCode() + '_' + option;

                selectPaymentMethodAction(this.getOptionData(method));
                checkoutData.setSelectedPaymentMethod(method);

                return true;
            },

            showLabel: function() {
                return true;
            },

            getAvailableOptions: function() {
                return window.checkoutConfig.payment.micuentaweb_other.availableOptions;
            },

            getRegroupMode: function() {
                return window.checkoutConfig.payment.micuentaweb_other.regroupMode;
            }
        });
    }
);
