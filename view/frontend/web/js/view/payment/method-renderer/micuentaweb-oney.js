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
        'jquery',
        'Lyranetwork_Micuentaweb/js/view/payment/method-renderer/micuentaweb-abstract'
    ],
    function($, Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Lyranetwork_Micuentaweb/payment/micuentaweb-oney',
                micuentawebOneyOption: window.checkoutConfig.payment.micuentaweb_oney.availableOptions ?
                    window.checkoutConfig.payment.micuentaweb_oney.availableOptions[0]['key'] : null
            },

            initObservable: function() {
                this._super().observe('micuentawebOneyOption');
                return this;
            },

            getData: function() {
                var data = this._super();
                data['additional_data']['micuentaweb_oney_option'] = this.micuentawebOneyOption();

                return data;
            },

            showLabel: function() {
                return true;
            },

            getAvailableOptions: function() {
                return window.checkoutConfig.payment.micuentaweb_oney.availableOptions;
            },

            getErrorMessage: function() {
                return $.cookie('micuentaweb_oney_error');
            },

            isPlaceOrderActionAllowed: function() {
                if ($.cookie('micuentaweb_oney_error')) {
                    return false;
                }

                return this._super();
            }
        });
    }
);
