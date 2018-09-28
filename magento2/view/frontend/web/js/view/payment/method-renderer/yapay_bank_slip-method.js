define(
    [
        'underscore',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Payment/js/model/credit-card-validation/credit-card-data',
        'Magento_Payment/js/model/credit-card-validation/credit-card-number-validator',
        'mage/translate',
        'Magento_Checkout/js/checkout-data'
    ],
    function (_, Component, creditCardData, cardNumberValidator, $t, checkoutData) {
        'use strict';

        return Component.extend({
            /**
             * Data model dos Boletos Bancários
             */
            defaults: {
                template: 'Yapay_Magento2/payment/yapay_bank_slip'
            },

            /**
             * Retorna o tipo de pagamento disponivel
             * @returns {string}
             */
            getCode: function() {
                return 'yapay_bank_slip';
            },

            /**
             * Construtor do yapay_transfence-method
             */
            initialize: function() {
                var self = this;
                this._super();
            },

            /**
             * Retorna que elemento esta ativo
             * @returns {boolean}
             */
            isActive: function () {
                return true;
            },

            /**
             * Retorna CPF do cliente
             * @returns {*}
             */
            getCpfCustomer: function () {
                //console.log(checkoutData.getShippingAddressFromData().custom_attributes);
                if(checkoutData.getShippingAddressFromData() != null) {
                    if (checkoutData.getShippingAddressFromData().custom_attributes != undefined) {
                        return checkoutData.getShippingAddressFromData().custom_attributes.cpf_customer;
                    }
                }
                return '';
            },

            /**
             * Retorna CNPJ do cliente
             * @returns {*}
             */
            getCnpjCustomer: function () {
                if(checkoutData.getShippingAddressFromData() != null) {
                    if (checkoutData.getShippingAddressFromData().custom_attributes != undefined) {
                        return checkoutData.getShippingAddressFromData().custom_attributes.cnpj_customer;
                    }
                }
                return '';
            },

            /**
             * Busca o Bairro do Cliente
             * @returns {*}
             */
            getNeighborhood: function() {
                if(checkoutData.getShippingAddressFromData() != null) {
                    if (checkoutData.getShippingAddressFromData().custom_attributes != undefined) {
                        return checkoutData.getShippingAddressFromData().custom_attributes.neighborhood_yapay;
                    }
                }
                return '';
            },


            /**
             * Envia request com os dados do boleto
             * @returns {{method: *, additional_data: {cc_number: *, cc_card: *, cc_exp_month: *, cc_exp_year: *, cc_cardholder: *, cc_installments: *, cc_security_code: *}}}
             */
            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'cpfCustomer': this.getCpfCustomer(),
                        'cnpjCustomer': this.getCnpjCustomer(),
                        'neighborhoodCustomer': this.getNeighborhood(),
                    }
                };
            },
        });
    }
);