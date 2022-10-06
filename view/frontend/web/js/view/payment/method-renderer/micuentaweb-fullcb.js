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
                template: 'Lyranetwork_Micuentaweb/payment/micuentaweb-fullcb',
                micuentawebFullcbOption: window.checkoutConfig.payment.micuentaweb_fullcb.availableOptions ?
                    window.checkoutConfig.payment.micuentaweb_fullcb.availableOptions[0]['key'] : null
            },

            initObservable: function() {
                this._super().observe('micuentawebFullcbOption');
                return this;
            },

            getData: function() {
                var data = this._super();
                data['additional_data']['micuentaweb_fullcb_option'] = this.micuentawebFullcbOption();

                return data;
            },

            getAvailableOptions: function() {
                return window.checkoutConfig.payment.micuentaweb_fullcb.availableOptions;
            }
        });
    }
);
