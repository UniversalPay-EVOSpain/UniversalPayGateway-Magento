
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'kxpay',
                component: 'Kineox_Kxpay/js/view/payment/method-renderer/kxpay-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);