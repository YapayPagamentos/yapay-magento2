define(
    [
        'underscore',
        'jquery',
        'ko',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/checkout-data',
        'Yapay_Magento2/js/model/credit-card-validation/credit-card-number-validator',
        'Yapay_Magento2/js/model/credit-card-validation/custom',
        'Magento_Payment/js/model/credit-card-validation/credit-card-data',
        'Magento_Checkout/js/action/select-shipping-address',
        'mage/translate'
    ],
    /**
     * Retorna metodos para view de checkout
     * @param _
     * @param $
     * @param ko
     * @param quote
     * @param priceUtils
     * @param Component
     * @param placeOrderAction
     * @param selectPaymentMethodAction
     * @param customer
     * @param checkoutData
     * @param cardNumberValidator
     * @param custom
     * @param creditCardData
     * @returns {*}
     */
    function (_,
        $,
        ko,
        quote,
        priceUtils,
        Component,
        placeOrderAction,
        selectPaymentMethodAction,
        customer,
        checkoutData,
        cardNumberValidator,
        custom,
        creditCardData,
        selectShippingAddressAction
    ) {
        'use strict';


        return Component.extend({
            /**
             * Data model dos cartões
             */
            defaults: {
                template: 'Yapay_Magento2/payment/yapay_credit_card',
                creditCardType: '',
                creditCardExpYear: '',
                creditCardExpMonth: '',
                creditCardNumber: '',
                creditCardSsStartMonth: '',
                creditCardSsStartYear: '',
                creditCardVerificationNumber: '',
                selectedCardType: null,
                creditCardholder: '',
                creditCardSecurityCode: '',
                creditCardInstallments: '',
                creditCardCcTypes: '',
                creditCardAvailableTypes: '',
                creditCardPaymentId: ''
            },
            initObservable: function () {
                /**
                 * Observador dos data models
                 */
                this._super()
                    .observe([
                        'creditCardType',
                        'creditCardExpYear',
                        'creditCardExpMonth',
                        'creditCardNumber',
                        'creditCardVerificationNumber',
                        'creditCardSsStartMonth',
                        'creditCardSsStartYear',
                        'selectedCardType',
                        'creditCardholder',
                        'creditCardSecurityCode',
                        'creditCardInstallments',
                        'creditCardCcTypes',
                        'creditCardAvailableTypes',
                        'creditCardPaymentId'
                    ]);
                return this;
            },

            /**
             * Retorna o tipo de pagamento disponivel
             * @returns {string}
             */
            getCode: function () {
                return 'yapay_credit_card';
            },

            /**
             * Construtor do yapay_credit_card-method
             */
            initialize: function () {
                var self = this;
                this._super();


                //Set credit card number to credit card data object
                this.creditCardNumber.subscribe(function (value) {
                    var result;
                    self.selectedCardType(null);
                    if (value == '' || value == null) {
                        return false;
                    }

                    result = cardNumberValidator(value);

                    if (!result.isPotentiallyValid && !result.isValid) {
                        return false;
                    }

                    if (result.card !== null) {
                        self.selectedCardType(result.card.title.toLowerCase());
                        creditCardData.creditCard = result.card;
                    }

                    if (result.isValid) {
                        creditCardData.creditCardNumber = value;
                        self.creditCardType(result.card.type);
                        self.creditCardPaymentId(result.card.payment_id);
                        // self.getSplitYapay();
                        self.getInstallmentsValues(result.card.payment_id);

                        // console.log(teste);
                        // var fp = window.yapay.FingerPrint({ env: '' });
                        // $('#finger_print').val(fp.getFingerPrint());

                        // $('input[name="payment[cc_number]"]').keypress(function(event){

                        // });
                    }
                });

                //Set expiration year to credit card data object
                this.creditCardExpYear.subscribe(function (value) {
                    creditCardData.expirationYear = value;
                });

                //Set expiration month to credit card data object
                this.creditCardExpMonth.subscribe(function (value) {
                    creditCardData.expirationYear = value;
                });

                //Set cvv code to credit card data object
                this.creditCardVerificationNumber.subscribe(function (value) {
                    creditCardData.cvvCode = value;
                });
                this.getInstallments();
            },

            /**
             * Retorna que elemento esta ativo
             * @returns {boolean}
             */
            isActive: function () {
                return true;
            },

            /**
             * Verifica ativação do parcelamento caso a bandeira permita
             * @returns {boolean}
             */
            isActiveSplit: function () {
                return this.creditCardPaymentId() !== '19' && this.creditCardPaymentId() !== '15';
            },

            /**
             * Retorna as imagens das bandeiras dos cartões
             *
             * @param type
             * @returns {boolean}
             */
            getIcons: function (type) {
                return window.checkoutConfig.payment.yapay_credit_card.icons.hasOwnProperty(type.toLowerCase()) ?
                    window.checkoutConfig.payment.yapay_credit_card.icons[type.toLowerCase()]
                    : false;
            },

            /**
             * Busca as bandeiras disponiveis
             * @returns {*}
             */
            getCcAvailableTypes: function () {
                return window.checkoutConfig.payment.yapay_credit_card.availableTypes;
            },

            /**
             * Buscas meses
             * @returns {*}
             */
            getCcMonths: function () {
                var months = {
                    1: "01 - Janeiro",
                    2: "02 - Fevereiro",
                    3: "03 - Março",
                    4: "04 - Abril",
                    5: "05 - Maio",
                    6: "06 - Junho",
                    7: "07 - Julho",
                    8: "08 - Agosto",
                    9: "09 - Setembro",
                    10: "10 - Outubro",
                    11: "11 - Novembro",
                    12: "12 - Dezembro"
                };
                return months;
            },

            /**
             * Buscas os anos
             * @returns {*}
             */
            getCcYears: function () {
                return window.checkoutConfig.payment.yapay_credit_card.years['yapay_credit_card'];
            },


            hasVerification: function () {
                return window.checkoutConfig.payment.yapay_credit_card.hasVerification['yapay_credit_card'];
            },

            /**
             * Busca os juros de cada parcela
             *
             * @returns {*}
             */
            getInterestInstallments: function () {
                return window.checkoutConfig.payment.yapay_credit_card.interestInstallments;
            },

            /**
             * Busca parcelas disponiveis
             *
             * @returns {Document.installments}
             */
            getInstallments: function () {
                return window.checkoutConfig.payment.yapay_credit_card.installments;
            },


            /**
             * Busca valor de parcela minima
             *
             * @returns {Document.valor_minimo}
             */
            getValorMinimo: function () {
                return window.checkoutConfig.payment.yapay_credit_card.valor_minimo;
            },

            /**
             * Busca API split Yapay
             *
             * @returns {Document.getSplitYapay}
             */
            getSplitYapay: function () {
                // console.log(totalOrder);
                return window.checkoutConfig.payment.yapay_credit_card.parcelamentoYapay;
            },

            // /**
            //  * Busca json Parcelamento Yapay
            //  *
            //  * @returns {Document.parcelamentoYapay}
            //  */
            //  getParcelamentoConfig: function() {
            //     return window.checkoutConfig.payment.yapay_credit_card.parcelamentoYapay;
            // },


            /**
             * Retorna as bandeiras disponiveis
             *
             * @returns {Array}
             */
            getCcAvailableTypesValues: function () {
                var cards = this.getCcAvailableTypes();
                var cardsArray = [];
                for (var i = 0; i < cards.length; i++) {
                    Object.keys(cards[i]).map(function (key, index) {
                        cardsArray[i] = { "value": key, "card": cards[i][key].toLowerCase() }
                    })
                }
                return cardsArray;
            },

            /**
             * Retorna meses
             *
             * @returns {*}
             */
            getCcMonthsValues: function () {
                return _.map(this.getCcMonths(), function (value, key) {
                    return {
                        'value': key,
                        'month': value
                    }
                });
            },

            /**
             * Retorna os Anos
             *
             * @returns {*}
             */
            getCcYearsValues: function () {
                return _.map(this.getCcYears(), function (value, key) {
                    return {
                        'value': key,
                        'year': value
                    }
                });
            },

            getInterestInstallmentsValues: function () {
                return this.getInterestInstallments()
            },
            /**
             * Retorna as pacelas disponiveis
             *
             * @returns {*}
             */
           
             getInstallmentsValues: function () {
                var $installmentArray = [];
                for (var i=0; i < this.getInstallments(); i++ ) {
                    $installmentArray[i] = i+1;
                }
                var total = quote.totals();
                var valorMinimo = this.getValorMinimo();
                var jsonSplit = JSON.parse(this.getSplitYapay());
                var installmentsConfig = this.getInstallments();
                var installmentsConfigInt = parseInt(installmentsConfig, 10);

                if (jsonSplit == null) {
                    return _.reduce(this.getInterestInstallmentsValues(), function(acc, value, key) {
                        var totalInterest = total.grand_total + (total.grand_total * parseFloat(value)/100);
                        var totalOrder = totalInterest.toFixed(2);
                        var installmentsValue = totalOrder / (key + 1);
                        var installmentsValueDecimal = installmentsValue.toFixed(2);
                        var valorMinimoDecimal = (Math.round(valorMinimo * 100) / 100).toFixed(2);    

                        if (parseFloat(installmentsValueDecimal) >= parseFloat(valorMinimoDecimal)) {
                            return [...acc, {
                                'value': key + 1,
                                'installment': key+1 + ' x R$' + installmentsValueDecimal + ' Total à Pagar = R$' + totalOrder
                            }]
                        } else {
                            return acc.length === 0 ? [{
                                'value': 1,
                                'installment': 1 + ' x R$' + (Math.round(totalOrder * 100) / 100).toFixed(2)  + ' Total à Pagar = R$' + totalOrder
    
                            }] : acc
                        }
    
                    }, []);                    
                } else if (jsonSplit.message_response.message == "error") {
                    return _.reduce(this.getInterestInstallmentsValues(), function(acc, value, key) {
                        var totalInterest = total.grand_total + (total.grand_total * parseFloat(value)/100);
                        var totalOrder = totalInterest.toFixed(2);
                        var installmentsValue = totalOrder / (key + 1);
                        var installmentsValueDecimal = installmentsValue.toFixed(2);
                        var valorMinimoDecimal = (Math.round(valorMinimo * 100) / 100).toFixed(2);    

                        if (parseFloat(installmentsValueDecimal) >= parseFloat(valorMinimoDecimal)) {
                            return [...acc, {
                                'value': key + 1,
                                'installment': key+1 + ' x R$' + installmentsValueDecimal + ' Total à Pagar = R$' + totalOrder
                            }]
                        } else {
                            return acc.length === 0 ? [{
                                'value': 1,
                                'installment': 1 + ' x R$' + (Math.round(totalOrder * 100) / 100).toFixed(2)  + ' Total à Pagar = R$' + totalOrder
    
                            }] : acc
                        }
    
                    }, []);
                } else if (jsonSplit.message_response.message == "success") {
                    var installmentsYapay = jsonSplit.data_response.payment_methods[0].splittings;
                    return _.reduce(this.getInterestInstallmentsValues(), function(acc, value, key) {
                        var totalInterest = total.grand_total + (total.grand_total * parseFloat(value)/100);
                        var totalOrder = totalInterest.toFixed(2);
                        var installmentsValue = totalOrder / (key + 1);
                        var installmentsValueDecimal = installmentsValue.toFixed(2);
                        var valorMinimoDecimal = (Math.round(valorMinimo * 100) / 100).toFixed(2);
    
                        for (var aux = 0; ((aux < installmentsConfigInt) && (aux < installmentsYapay.length)); aux++) {
                            if (parseFloat(installmentsYapay[key].value_split) >= parseFloat(valorMinimoDecimal)) {
                                return [...acc, {
                                    'value': key + 1,
                                    'installment': parseInt(installmentsYapay[key].split) + ' x R$' + installmentsYapay[key].value_split + ((installmentsYapay[key].split_rate == 0) ? " sem " : " com ") + "juros"
                                }]
                            } else {
                                return acc.length === 0 ? [{
                                    'value': 1,
                                    'installment': 1 + ' x R$' + totalOrder + " sem juros"
    
                                }] : acc
                            }
    
                        }
                        // if (parseFloat(installmentsValueDecimal) >= parseFloat(valorMinimoDecimal)) {
                        //     return [...acc, {
                        //         'value': key + 1,
                        //         'installment': key+1 + ' x R$' + installmentsValueDecimal + ' Total à Pagar = R$' + totalOrder
                        //     }]
                        // } else {
                        //     return acc.length === 0 ? [{
                        //         'value': 1,
                        //         'installment': 1 + ' x R$' + (Math.round(totalOrder * 100) / 100).toFixed(2)  + ' Total à Pagar = R$' + totalOrder
    
                        //     }] : acc
                        // }
    
                    }, []);
                }


            },
           

            /**
             * Retorna CPF do cliente
             * @returns {*}
             */
            getCpfCustomer: function () {
                if (checkoutData.getShippingAddressFromData() != null) {
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
                if (checkoutData.getShippingAddressFromData() != null) {
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
            getNeighborhood: function () {
                if (checkoutData.getShippingAddressFromData() != null) {
                    if (checkoutData.getShippingAddressFromData().custom_attributes != undefined) {
                        return checkoutData.getShippingAddressFromData().custom_attributes.neighborhood_yapay;
                    }
                }
                return '';
            },


            /**
             * Envia request com os dados do cartão
             * @returns {{method: *, additional_data: {cc_number: *, cc_card: *, cc_exp_month: *, cc_exp_year: *, cc_cardholder: *, cc_installments: *, cc_security_code: *}}}
             */
            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'cc_number': this.creditCardNumber(),
                        'cc_card': this.creditCardPaymentId(),
                        'cc_exp_month': this.creditCardExpMonth(),
                        'cc_exp_year': this.creditCardExpYear(),
                        'cc_cardholder': jQuery('#' + this.getCode() + '_cc_cardholder').val(),
                        'cc_installments': jQuery('#' + this.getCode() + 'cc_installments').val(),
                        'cc_security_code': jQuery('#' + this.getCode() + 'cc_security_code').val(),
                        'cpfCustomer': this.getCpfCustomer(),
                        'cnpjCustomer': this.getCnpjCustomer(),
                        'neighborhoodCustomer': this.getNeighborhood(),
                    }
                };
            },

            /**
             * Fecha a ordem
             * @param self
             * @param paymentData
             * @param messageContainer
             * @param pagseguroHash
             */
            finishOrder: function (self, paymentData, messageContainer, pagseguroHash) {
                console.log('oi sou finish Order');

            },

            /**
             * Chama os metodos de validação
             * @returns {*}
             */
            validate: function () {
                var $form = $('#' + this.getCode() + '-form');
                return $form.validation() && $form.validation('isValid');
            }

        });
    }
);
