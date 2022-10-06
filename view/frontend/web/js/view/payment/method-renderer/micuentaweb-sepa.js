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
                template: 'Lyranetwork_Micuentaweb/payment/micuentaweb-sepa',
                micuentawebUseIdentifier: 1,
            },

            initObservable: function() {
                this._super();
                this.observe('micuentawebUseIdentifier');

                return this;
            },

            getData: function() {
                var data = this._super();

                if (this.isOneClick()) {
                    data['additional_data']['micuentaweb_sepa_use_identifier'] = this.micuentawebUseIdentifier();
                }

                return data;
            },

            isOneClick: function() {
                return window.checkoutConfig.payment[this.item.method].oneClick || false;
            },

            getMaskedPan: function() {
                return window.checkoutConfig.payment[this.item.method].maskedPan || null;
            },

            updatePaymentBlock: function(blockName) {
                $('.payment-method._active .payment-method-content .micuentaweb-identifier li.micuentaweb-sepa-block').hide();
                $('li.micuentaweb-sepa-' + blockName + '-block').show();
            },
        });
    }
);
