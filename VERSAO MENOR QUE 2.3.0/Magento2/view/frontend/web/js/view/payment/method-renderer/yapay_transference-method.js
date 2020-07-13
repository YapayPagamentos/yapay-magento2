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
             * Data model das transferencias
             */
            defaults: {
                template: 'Yapay_Magento2/payment/yapay_transference',
                meansOfPaymentAvailable: '',
                transferenceNumber: '',
            },
            /**
             * Observador dos data models
             */
            initObservable: function () {
                this._super()
                    .observe([
                        'meansOfPaymentAvailable',
                        'transferenceNumber',
                    ]);
                return this;
            },

            /**
             * Retorna o tipo de pagamento disponivel
             * @returns {string}
             */
            getCode: function() {
                return 'yapay_transference';
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
             * Busca tipos de transferencias disponiveis
             * @returns {*}
             */
            getMeansOfPaymentAvailable: function()  {
                return window.checkoutConfig.payment.yapay_transference.available_payment_transference_yapay;
            },

            /**
             * Retorna tipos de transferencias disponiveis
             * @returns {Array}
             */
            getMeansOfPaymentAvailableValues: function() {
                var meansOfPayment = '['+this.getMeansOfPaymentAvailable()+']';
                meansOfPayment = JSON.parse(meansOfPayment);
                var meansOfPaymentArray = [];
                for(var i=0; i < meansOfPayment.length; i++) {
                    Object.keys(meansOfPayment[i]).map(function(key, index) {
                        meansOfPaymentArray[i] = {"value":key, "paymentMethod": meansOfPayment[i][key]}
                    })
                }
                return meansOfPaymentArray;
            },

            /**
             * Envia request com os dados da transferencia
             * @returns {{method: *, additional_data: {cc_number: *, cc_card: *, cc_exp_month: *, cc_exp_year: *, cc_cardholder: *, cc_installments: *, cc_security_code: *}}}
             */
            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'payment_method_id': jQuery('#'+this.getCode()+'tt_paymentMethod').val(),
                        'cpfCustomer': this.getCpfCustomer(),
                        'cnpjCustomer': this.getCnpjCustomer(),
                        'neighborhoodCustomer': this.getNeighborhood(),
                    }
                };
            },
        });
    }
);