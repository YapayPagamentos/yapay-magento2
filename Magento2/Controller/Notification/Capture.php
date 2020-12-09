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
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory;

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

    protected $orderFactory;

    protected $orderRepository;

    protected $orderItems;

    /**
     * @var TransactionFactory
     */
    protected $_transactionFactory;

    public function __construct(Context $context, OrderManagementInterface $orderManagement,   OrderRepositoryInterface $orderRepository, \Magento\Sales\Model\OrderFactory $orderFactory, CollectionFactory $orderItems )
    {
        parent::__construct($context);
        $this->orderManagement = $orderManagement;
        $this->paymentApi = $context->getObjectManager()->get(PaymentApi::class);
        $this->helper = $context->getObjectManager()->get(YapayData::class);
        $this->_transaction  = $context->getObjectManager()->get(Transaction::class);
        $this->_transactionFactory = $context->getObjectManager()->get(TransactionFactory::class);
        $this->orderFactory = $orderFactory;
        $this->orderRepository = $orderRepository;
        $this->_orderItems = $orderItems;

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



        // \Magento\Framework\App\ObjectManager::getInstance()
        // ->get('Psr\Log\LoggerInterface')
        // ->debug($this->getBaseURL());

        // die();
        $transaction = $response->data_response->transaction;
        $transactionId = $transaction->transaction_id;
        $statusId = $transaction->status_id;
        $statusName = $transaction->status_name;
        $order_number = $transaction->order_number;

        // \Magento\Framework\App\ObjectManager::getInstance()
        // ->get('Psr\Log\LoggerInterface')
        // ->debug($transactionId);

        // echo '<br><pre>';
        // var_dump($response);

        echo('Notificação OK. Pedido: '.$order_number.' Status: '.$statusName);

        $model = $this->_transactionFactory->create([]);
        $this->_transaction->load($model, $order_number, 'txn_id');
        $order = $model->getOrder();


        // \Magento\Framework\App\ObjectManager::getInstance()
        // ->get('Psr\Log\LoggerInterface')
        // ->debug(json_encode($order_number = $transaction->order_number));


        if ($statusId == self::STATUS_APPROVED) {
            $this->payed($order, $transactionId, $statusName);
        }

        if ($statusId == self::STATUS_CANCELED || $statusId == self::STATUS_REPROVED) {
            $this->cancel($order, $transactionId, $statusName);
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
            'Campainha recebida vinda do Yapay: Transaction ID '.$transactionId. ' - Status '.$statusName, true
        );

        $order->save();
    }

    /**
     * Método de notificação de pagamento realizado
     *
     * @param $order
     * @param $transactionId
     */
    protected function payed($order, $transactionId, $statusName)
    {
        $this->changePayment($order, $transactionId, true, $statusName);

        $order->setState(Order::STATE_PROCESSING);
        $order->setStatus(Order::STATE_PROCESSING);
    }

    /**
     * Método de notificação de cancelamento de pagamento
     *
     * @param $order
     * @param $transactionId
     */
    protected function cancel($order, $transactionId, $statusName)
    {
        $order->setState(Order::STATE_CANCELED);
        $order->setStatus(Order::STATE_CANCELED);

        $this->changePayment($order, $transactionId, false, $statusName);
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
    protected function changePayment($order, $transactionId, $isPayed, $statusName)
    {
        //invoices
        $invoices = $order->getInvoiceCollection();

        foreach ($invoices as $invoice) {
            if ($invoice->getData('transaction_id') != $transactionId) {
                // continue;
                if ($isPayed) {
                    $invoice->pay();
                }else{
                    $invoice->setState(Order\Invoice::STATE_CANCELED);
                }
            }
            $invoice->save();
        }

        $order = $this->orderRepository->get($order->getId());

        if ($order->getState() == 'canceled') {
            $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED)->save();
            $order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED)->save();

            $items = $order->getItemsCollection();
            foreach ($items as $item) {
                $item->setData('qty_canceled',$item->getQtyOrdered())->save();
                // $item->setData('qty_invoiced',0)->save();
                $item->setStatus(\Magento\Sales\Model\Order\Item::STATUS_CANCELED)->save();
            }

            //Start of comment about order cancel
            $comment =  'Campainha recebida vinda do Yapay: Transaction ID '.$transactionId. ' - Status '.$statusName;
            $history = $order->addStatusHistoryComment($comment);
            $history->save();
            //End of comment about order cancel

            $this->_orderItems->save($items);

            $this->orderRepository->save($order);

        }

        // if($order->getState() == 'canceled') {
        //     $IdtransacaoYapay = $transactionId;
        //     $transaction_id = $order->getOwnId();
        //     $payment = $order->getPayment();
        //     $transactionId = $payment->getLastTransId();
        //     $method = $payment->getMethodInstance();
        //     $description_for_customer = "O pedido ".$transactionId. " - ID Yapay " .$IdtransacaoYapay." foi cancelado. Enviado pela Yapay";

        //     $method->fetchTransactionInfo($payment, $transactionId, $description_for_customer);

        //     $items = $order->getItemsCollection();

        //     foreach ($items as $item) {
        //         if ($item->getProductType() != 'configurable') {
        //             $item->setQtyCanceled((double)$item->getQtyInvoiced());
        //             $item->setStatus(\Magento\Sales\Model\Order\Item::STATUS_CANCELED);
        //             $item->save();
        //         }
        //     }

        //     $this->addCancelDetails($description_for_customer, $order);
        // }

        // \Magento\Framework\App\ObjectManager::getInstance()
        // ->get('Psr\Log\LoggerInterface')
        // ->debug($item->getStatusId());


    }

    // private function addCancelDetails($comment, $order){
	// 	$status = $this->orderManagement->getStatus($order->getEntityId());
	// 	$history = $order->addStatusHistoryComment($comment, $status);
	//     $history->setIsVisibleOnFront(1);
	//     $history->setIsCustomerNotified(0);
	//     $history->save();
	//     $comment = trim(strip_tags($comment));
	//     $order->save();
	//     $this->_orderCommentSender->send($order, 1, $comment);
	//     return $this;
	// }

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
