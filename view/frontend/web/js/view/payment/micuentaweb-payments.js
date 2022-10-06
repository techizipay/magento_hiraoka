/**
 * Copyright © Lyra Network.
 * This file is part of Mi Cuenta Web plugin for Magento 2. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list',
        'Magento_Checkout/js/view/payment/default'
    ],
    function(Component, rendererList) {
        'use strict';
        rendererList.push(
            {
                type: 'micuentaweb_standard',
                component: 'Lyranetwork_Micuentaweb/js/view/payment/method-renderer/micuentaweb-standard'
            },
            {
                type: 'micuentaweb_multi',
                component: 'Lyranetwork_Micuentaweb/js/view/payment/method-renderer/micuentaweb-multi'
            },
            {
                type: 'micuentaweb_gift',
                component: 'Lyranetwork_Micuentaweb/js/view/payment/method-renderer/micuentaweb-gift'
            },
            {
                type: 'micuentaweb_choozeo',
                component: 'Lyranetwork_Micuentaweb/js/view/payment/method-renderer/micuentaweb-choozeo'
            },
            {
                type: 'micuentaweb_oney',
                component: 'Lyranetwork_Micuentaweb/js/view/payment/method-renderer/micuentaweb-oney'
            },
            {
                type: 'micuentaweb_fullcb',
                component: 'Lyranetwork_Micuentaweb/js/view/payment/method-renderer/micuentaweb-fullcb'
            },
            {
                type: 'micuentaweb_sepa',
                component: 'Lyranetwork_Micuentaweb/js/view/payment/method-renderer/micuentaweb-sepa'
            },
            {
                type: 'micuentaweb_paypal',
                component: 'Lyranetwork_Micuentaweb/js/view/payment/method-renderer/micuentaweb-paypal'
            },
            {
                type: 'micuentaweb_franfinance',
                component: 'Lyranetwork_Micuentaweb/js/view/payment/method-renderer/micuentaweb-franfinance'
            },
            {
                type: 'micuentaweb_other',
                component: 'Lyranetwork_Micuentaweb/js/view/payment/method-renderer/micuentaweb-other'
            }
        );

        /** Add view logic here if needed */
        return Component.extend({});
    }
);
