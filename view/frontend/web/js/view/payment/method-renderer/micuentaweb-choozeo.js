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
        'Lyranetwork_Micuentaweb/js/view/payment/method-renderer/micuentaweb-abstract'
    ],
    function(Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Lyranetwork_Micuentaweb/payment/micuentaweb-choozeo',
                micuentawebChoozeoOption: window.checkoutConfig.payment.micuentaweb_choozeo.availableOptions ?
                    window.checkoutConfig.payment.micuentaweb_choozeo.availableOptions[0]['key'] : null
            },

            initObservable: function() {
                this._super().observe('micuentawebChoozeoOption');
                return this;
            },

            getData: function() {
                var data = this._super();
                data['additional_data']['micuentaweb_choozeo_option'] = this.micuentawebChoozeoOption();

                return data;
            },

            showLabel: function() {
                return true;
            },

            getAvailableOptions: function() {
                return window.checkoutConfig.payment.micuentaweb_choozeo.availableOptions;
            }
        });
    }
);
