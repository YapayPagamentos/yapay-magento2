<?php

namespace Yapay\Magento2\Model\Payment;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ObjectManager;
use Magento\Payment\Model\InfoInterface;
use Yapay\Magento2\Helper\YapayData;

abstract class PaymentAbstract extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * @var bool
     */
    protected $_isOffline = true;
    protected $_canCapture = true;
    protected $_canRefund = true;
    protected $_isGateway               = true;
    protected $_canCapturePartial       = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = true;
    protected $_canCancel               = true;
    protected $_canUseForMultishipping  = false;
    protected $_canReviewPayment        = true;
    protected $_countryFactory;
    protected $_supportedCurrencyCodes = ['BRL'];
    protected $order_status = 'pending_payment';



    /**
     * Method responsible by find the request and dispatch the bind
     *
     * @param \Magento\Framework\DataObject $data
     * @return $this|\Magento\Payment\Model\Method\AbstractMethod
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);

        $infoInstance = $this->getInfoInstance();
        $currentData = $data->getAdditionalData();

        foreach ($currentData as $key => $value) {
            $infoInstance->setAdditionalInformation($key, $value);
        }
        return $this;
    }

    /**
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {

        if (is_null($payment->getParentTransactionId())) {

            $this->_logger->debug(json_encode($this->getConfigData('payment_action')));
            $this->authorize($payment, $amount);
        }


        $payment->setIsTransactionPending(true);


        return $this;
    }

    /**
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return $this
     */
    public function authorize(InfoInterface $payment, $amount)
    {
        if ($amount <= 0) {
            throw new LocalizedException(__('Invalid amount for authorization.'));
        }

        $objectManager = ObjectManager::getInstance();
        $helper = $objectManager->get(YapayData::class);
        $helper->generateTransaction($payment);

        return false;
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

    public function order(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $this->_logger->debug(json_encode('oi order'));
    }


}