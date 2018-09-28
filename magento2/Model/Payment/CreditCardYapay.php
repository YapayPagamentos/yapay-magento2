<?php

namespace Yapay\Magento2\Model\Payment;

use Yapay\Magento2\Helper\Data;
use Yapay\Magento2\Helper\ValidationYapay;


class CreditCardYapay extends PaymentAbstract
{
    /**
     * Constante que indica qual tipo de pagamento corresponde a classe
     */
    const CODE = 'yapay_credit_card';

    /**
     * @var string
     */
    protected $_code = self::CODE;


    /**
     * @inheritdoc
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {

        parent::assignData($data);
        $infoInstance = $this->getInfoInstance();
        $currentData = $data->getAdditionalData();
        $data->getData(json_encode($this->_scopeConfig));
        $this->_logger->debug(json_encode($data->getData()));
        $helper = new ValidationYapay($this->_scopeConfig);

        $helper->validateCreditCard($data->getData());

        foreach($currentData as $key=>$value){
            $infoInstance->setAdditionalInformation($key,$value);
        }
        return $this;

    }


    /**
     * Metodo proprio do magento que busca configurações de pagameno no modulo do magento
     *
     * @return string
     */
    public function getConfigPaymentAction()
    {
        return self::ACTION_AUTHORIZE_CAPTURE;
    }

}