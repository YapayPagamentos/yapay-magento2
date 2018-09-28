<?php

namespace Yapay\Magento2\Model\Payment;


use Yapay\Magento2\Helper\Data;

class TransferenceYapay extends PaymentAbstract
{
    /**
     * Constante que indica qual tipo de pagamento corresponde a classe
     */
    const CODE = 'yapay_transference';

    /**
     * @var string
     */
    protected $_code = self::CODE;
    
}