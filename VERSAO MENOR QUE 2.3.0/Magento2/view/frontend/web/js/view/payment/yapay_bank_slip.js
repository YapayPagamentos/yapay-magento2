define([
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],

    /**
     * Chama o yapay_bank_slip-method
     * @param Component
     * @param rendererList
     * @returns {*}
     */
    function (Component, rendererList) {
        'use strict';

        rendererList.push(
            {
                type: 'yapay_bank_slip',
                component: 'Yapay_Magento2/js/view/payment/method-renderer/yapay_bank_slip-method'
            }
        );

        /** Add view logic here if needed */
        return Component.extend({});
    });
