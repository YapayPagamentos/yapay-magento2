/*jshint browser:true jquery:true*/
/*global alert*/
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define([
            'jquery',
            'Magento_Payment/js/model/credit-card-validation/cvv-validator',
            'Yapay_Magento2/js/model/credit-card-validation/credit-card-number-validator',
            'Magento_Payment/js/model/credit-card-validation/expiration-date-validator/expiration-year-validator',
            'Magento_Payment/js/model/credit-card-validation/expiration-date-validator/expiration-month-validator',
            'Magento_Payment/js/model/credit-card-validation/credit-card-data',
			'mage/translate'
        ], factory);
    } else {
        factory(jQuery);
    }
}(function ($, cvvValidator, creditCardNumberValidator, expirationDateValidator, monthValidator, creditCardData) {
    "use strict";
	 var creditCartTypes = {
        'visa': [new RegExp('^4[0-9]{12}([0-9]{3})?$'), new RegExp('^[0-9]{3}$'), true],
        'mastercard': [new RegExp('^5([1-5]\\d*)?$'), new RegExp('^[0-9]{3}$'), true],
        'amex': [new RegExp('^3([47]\\d*)?$'), new RegExp('^[0-9]{4}$'), true],
        'discover': [new RegExp('^6(?:011|5[0-9]{2})[0-9]{12}$'), true],
        'elo': [new RegExp('^(636368|438935|504175|451416|636297|5067|4576|4011|50904|50905|50906)'), true],
        'diners': [new RegExp('^3((0([0-5]\\d*)?)|[689]\\d*)?$'), new RegExp('^[0-9]{3}$'), true],
        'hipercard': [new RegExp('^(606282|3841)[0-9]{5,}$'), new RegExp('^[0-9]{3}$'), true],
		'hiper': [new RegExp('^(637095|637612|637599|637609|637568)'), true],
		'aura': [new RegExp('^50[0-9]{14,17}$'), true],
        'jcb': [new RegExp('^(3(?:088|096|112|158|337|5(?:2[89]|[3-8][0-9]))\\d{12})$'), true]
    };
    $.each({
		'validate-card-type-yapay': [
            function (number, item, allowedTypes) {
                var cardInfo,
                    i,
                    l;

                if (!creditCardNumberValidator(number).isValid) {
                    return false;
                } else {
                    cardInfo = creditCardNumberValidator(number).card;
                    for (i = 0, l = allowedTypes.length; i < l; i++) {
                        if (cardInfo.title.toLowerCase() == allowedTypes[i].card.toLowerCase()) {
                            return true;
                        }
                    }
                    return false;
                }
            },
            $.mage.__('Credit card number does not match credit card type.')
        ]
    }, function (i, rule) {
        rule.unshift(i);
        $.validator.addMethod.apply($.validator, rule);
    });
}));