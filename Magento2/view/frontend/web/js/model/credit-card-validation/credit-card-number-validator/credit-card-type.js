/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'mageUtils'
    ],
    function ($, utils) {
        'use strict';
        var types = [
			// {
            //     title: 'Discover',
            //     type: 'discover',
            //     payment_id: '15',
            //     pattern: '^6(?:011|5[0-9]{2})[0-9]{12}$',
            //     gaps: [4, 8, 12],
            //     lengths: [16],
            //     code: {
            //         name: 'CID',
            //         size: 3
            //     }
            // },
            {
                title: 'Visa',
                type: 'visa',
                payment_id: '3',
                pattern: '^4\\d*$',
                gaps: [4, 8, 12, 20],
                lengths: [16],
                code: {
                    name: 'CVV',
                    size: 3
                }
            },
            {
                title: 'Mastercard',
                type: 'mastercard',
                payment_id: '4',
                pattern: '^5([1-5]\\d*)?$',
                gaps: [4, 8, 12],
                lengths: [16],
                code: {
                    name: 'CVC',
                    size: 3
                }
            },
            {
                title: 'American Express',
                type: 'amex',
                payment_id: '5',
                pattern: '^3([47]\\d*)?$',
                isAmex: true,
                gaps: [4, 10],
                lengths: [15],
                code: {
                    name: 'CID',
                    size: 4
                }
            },
            {
                title: 'Diners',
                type: 'diners',
                payment_id: '2',
                pattern: '^3((0([0-5]\\d*)?)|[689]\\d*)?$',
                gaps: [4, 10],
                lengths: [14],
                code: {
                    name: 'CVV',
                    size: 3
                }
            },
			{
                title: 'Hipercard',
                type: 'hipercard',
                payment_id: '20',
                pattern: '^(606282|3841)[0-9]{5,}$',
                gaps: [4, 8, 12],
                lengths: [13,16,19],
                code: {
                    name: 'CVV',
                    size: 3
                }
            },
            // {
            //     title: 'Aura',
            //     type: 'aura',
            //     payment_id: '18',
            //     pattern: '^50[0-9]{14,17}$',
            //     gaps: [4, 8, 12],
            //     lengths: [12, 13, 14, 15, 16, 17, 18, 19],
            //     code: {
            //         name: 'CVV',
            //         size: 3
            //     }
            // },
            {
                title: 'Elo',
                type: 'elo',
                payment_id: '16',
                pattern: /^401178|^401179|^431274|^438935|^451416|^457393|^457631|^457632|^504175|^627780|^636297|^636369|^636368|^(506699|5067[0-6]\d|50677[0-8])|^(50900\d|5090[1-9]\d|509[1-9]\d{2})|^65003[1-3]|^(65003[5-9]|65004\d|65005[0-1])|^(65040[5-9]|6504[1-3]\d)|^(65048[5-9]|65049\d|6505[0-2]\d|65053[0-8])|^(65054[1-9]|6505[5-8]\d|65059[0-8])|^(65070\d|65071[0-8])|^65072[0-7]|^(65090[1-9]|65091\d|650920)|^(65165[2-9]|6516[6-7]\d)|^(65500\d|65501\d)|^(65502[1-9]|6550[3-4]\d|65505[0-8])|^(65092[1-9]|65097[0-8])/,
                gaps: [4, 8, 12],
                lengths: [16],
                code: {
                    name: 'CVV',
                    size: 3
                }
            },
            {
                title: 'HIPER',
                type: 'hiper',
                payment_id: '25',
                pattern: '^(637095|637612|637599|637609|637568)',
                gaps: [4, 8, 12],
                lengths: [12, 13, 14, 15, 16, 17, 18, 19],
                code: {
                    name: 'CVV',
                    size: 3
                }
            },
            // {
            //     title: 'JCB',
            //     type: 'jcb',
            //     payment_id: '19',
            //     pattern: '^(3(?:088|096|112|158|337|5(?:2[89]|[3-8][0-9]))\\d{12})$',
            //     gaps: [4, 8, 12],
            //     lengths: [12, 13, 14, 15, 16, 17, 18, 19],
            //     code: {
            //         name: 'CVV',
            //         size: 3
            //     }
            // }

        ];
        return {
            getCardTypes: function (cardNumber) {

                var i, value,
                    result = [];
                if (utils.isEmpty(cardNumber)) {
                    return result;
                }

                if (cardNumber === '') {
                    return $.extend(true, {}, types);
                }

                for (i = 0; i < types.length; i++) {
                    value = types[i];
                    if (new RegExp(value.pattern).test(cardNumber)) {
                        result.push($.extend(true, {}, value));
                    }
                }
                console.log(result)
                if(result.length > 1) {
                    console.log([result[1]])
                    return [result[1]]
                }
                return result;
            }
        }
    }
);
