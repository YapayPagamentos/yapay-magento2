<?php

namespace Yapay\Magento2\Controller\Notification;


use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\TransactionFactory;
use \Magento\Sales\Model\ResourceModel\Order\Payment\Transaction;
use Yapay\Magento2\Helper\YapayData;
use Yapay\Magento2\Api\PaymentApi;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;

class Capture extends Action implements CsrfAwareActionInterface
{
    const STATUS_WAITING  = 4;
    const STATUS_PROCESSING = 5;
    const STATUS_APPROVED = 6;
    const STATUS_CANCELED = 7;
    const STATUS_CONFLICT = 24;
    const STATUS_MONITORING = 87;
    const STATUS_RECOVER = 88;
    const STATUS_REPROVED = 89;
    const URL_SANDBOX = "https://api.intermediador.sandbox.yapay.com.br/";
    const URL_PRODUCTION = "https://api.intermediador.yapay.com.br/";

    /**
     * @var YapayData
     */
    protected $helper;

    /**
     * @var PaymentApi
     */
    protected $paymentApi;
    /**
     * @var Transaction
     */
    protected $_transaction;

    /**
     * @var TransactionFactory
     */
    protected $_transactionFactory;

    public function __construct(Context $context)
    {
        parent::__construct($context);
        $this->paymentApi = $context->getObjectManager()->get(PaymentApi::class);
        $this->helper = $context->getObjectManager()->get(YapayData::class);
        $this->_transaction  = $context->getObjectManager()->get(Transaction::class);
        $this->_transactionFactory = $context->getObjectManager()->get(TransactionFactory::class);
    }

    /**
     * Execute action based on request and return result
     *
     * @return bool|ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {

        $tokenTransaction = $this->getRequest()->getParam('token_transaction');

        if (!$tokenTransaction) {
            return false;
        }

        $response = json_decode($this->paymentApi->getTransactionByTransactionToken($tokenTransaction, $this->getBaseURL()));

        \Magento\Framework\App\ObjectManager::getInstance()
        ->get('Psr\Log\LoggerInterface')
        ->debug(json_encode($responde));


        $transaction = $response->data_response->transaction;
        $transactionId = $transaction->transaction_id;
        $statusId = $transaction->status_id;
        $statusName = $transaction->status_name;

        $model = $this->_transactionFactory->create([]);
        $this->_transaction->load($model, $transactionId, 'txn_id');
        $order = $model->getOrder();

        if ($statusId == self::STATUS_APPROVED) {
            $this->payed($order, $transactionId);
        }

        if ($statusId == self::STATUS_CANCELED || $statusId == self::STATUS_REPROVED) {
            $this->cancel($order, $transactionId);
        }

        if ($statusId == self::STATUS_CONFLICT) {
            $this->conflict($order);
        }

        if ($statusId == self::STATUS_MONITORING ||
            $statusId == self::STATUS_PROCESSING ||
            $statusId == self::STATUS_WAITING ||
            $statusId == self::STATUS_RECOVER) {
            $this->waiting($order);
        }

        $order->addStatusToHistory(
            $order->getStatus(),
            'Campainha recebida vinda do Yapay: Transaction ID '.$transactionId. ' - Status '.$statusName
        );

        $order->save();
    }

    /**
     * Método de notificação de pagamento realizado
     *
     * @param $order
     * @param $transactionId
     */
    protected function payed($order, $transactionId)
    {
        $this->changePayment($order, $transactionId, true);

        $order->setState(Order::STATE_PROCESSING);
        $order->setStatus(Order::STATE_PROCESSING);
    }

    /**
     * Método de notificação de cancelamento de pagamento
     *
     * @param $order
     * @param $transactionId
     */
    protected function cancel($order, $transactionId)
    {
        $order->setState(Order::STATE_CANCELED);
        $order->setStatus(Order::STATE_CANCELED);

        $this->changePayment($order, $transactionId, false);
    }

    /**
     * Mpetodo de notificação de disputa
     *
     * @param $order
     */
    protected function conflict($order)
    {
        $order->setState(Order::STATE_PAYMENT_REVIEW);
        $order->setStatus(Order::STATE_PAYMENT_REVIEW);
    }

    /**
     * Método de notificação de aguardando pagamento
     *
     * @param $order
     */
    protected function waiting($order)
    {
        $order->setState(Order::STATE_PENDING_PAYMENT);
        $order->setStatus(Order::STATE_PENDING_PAYMENT);
    }

    /**
     * Método que troca o status da fatura
     * @param $order
     * @param $transactionId
     * @param $isPayed
     */
    protected function changePayment($order, $transactionId, $isPayed)
    {
        $invoices = $order->getInvoiceCollection();

        foreach ($invoices as $invoice) {
            if ($invoice->getData('transaction_id') != $transactionId) {
                continue;
            }

            if ($isPayed) {
                $invoice->pay();
            }else{
                $invoice->setState(Order\Invoice::STATE_CANCELED);
            }

            $invoice->save();
        }
    }

    public function getBaseURL()
    {
        if ($this->getEnvironment() == 'production') {
            return self::URL_PRODUCTION;
        }
        return self::URL_SANDBOX;
    }

    public function getEnvironment()
    {
        $objectManager = ObjectManager::getInstance();
        $scopeConfig = $objectManager->get(ScopeConfigInterface::class);
        $environment = $scopeConfig->getValue('payment/yapay_configuration/environment_configuration_yapay', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $environment;
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

}
