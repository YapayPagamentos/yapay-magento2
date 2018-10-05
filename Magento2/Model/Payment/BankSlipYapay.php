<?php

namespace Yapay\Magento2\Model\Payment;



class BankSlipYapay extends PaymentAbstract
{
    /**
     * Constante que indica qual tipo de pagamento corresponde a classe
     */
    const CODE = 'yapay_bank_slip';

    /**
     * @var string
     */
    protected $_code = self::CODE;


}