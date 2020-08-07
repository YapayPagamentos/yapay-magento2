<?php

namespace Yapay\Magento2\Helper;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\PaymentException;
use Magento\Framework\Phrase;
use Magento\Sales\Model\Order;
use Yapay\Magento2\Api\PaymentApi;


class YapayData extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Cart
     */
    protected $_cart;

    protected $paymentApi;

    const URL_SANDBOX = "https://api.intermediador.sandbox.yapay.com.br/";
    const URL_PRODUCTION = "https://api.intermediador.yapay.com.br/";
    const TYPE_ADDRESS = 'B';
    const ATT_CPF_OR_CNPJ_KEY = 'cpf_or_cnpj';
    const ATTR_PHONE_KEY = 'phone';
    const PAYMENT_METHOD_BILLET = 6;

    /**
     * YapayData constructor.
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Cart $cart
     */
    public function __construct(
        Context $context,
        Cart $cart
    )
    {
        $objectManager = ObjectManager::getInstance();
        $this->_scopeConfig = $objectManager->get(ScopeConfigInterface::class);
        $this->_cart = $cart;
        parent::__construct($context);
    }

    /**
     * Responsavel por setar o tipo de ambiente sandbox ou produção
     *
     * @return string
     */
    public function getBaseURL()
    {
        if ($this->getEnvironment() == 'production') {
            return self::URL_PRODUCTION;
        }
        return self::URL_SANDBOX;
    }

    /**
     * Responsavel por validar o cpf e cnpj
     * @param $cpf
     * @param $cnpj
     * @return mixed
     */
    public function checkCpfAndCnpj($cpf, $cnpj)
    {
        $cpf = preg_replace( '#[^0-9]#', '', $cpf);
        $customer[0] = $cpf;
        if($cnpj != null) {
            $cnpj = preg_replace( '#[^0-9]#', '', $cnpj);
            $customer[1] = $cnpj;
        }
        return $customer;
    }


    /**
     * Trata os dados do checkout como visitante
     *
     * @param $paymentData
     * @return array
     * @throws LocalizedException
     */
    public function getCheckoutVisitant($paymentData) {
        $order = $paymentData->getOrder();

        $checkCpfAndCnpj = $this->checkCpfAndCnpj($paymentData->getData('additional_information')['cpfCustomer'] , $paymentData->getData('additional_information')['cnpjCustomer'] );

        $number_contact = str_replace(" ","",$order->getBillingAddress()->getData('telephone'));
        $number_contact = str_replace("(","",$number_contact);
        $number_contact = str_replace(")","",$number_contact);
        $number_contact = str_replace("-","",$number_contact);



        $customerCheckout =
            [
                'name' => $order->getBillingAddress()->getData('firstname') . ' ' . $order->getBillingAddress()->getData('lastname') ,
                'email' => $order->getBillingAddress()->getData('email'),
                'cpf' =>  $checkCpfAndCnpj[0],
                'cnpj' => $checkCpfAndCnpj[1] ?? "",
                'company_name' => 'Não Informado',
                'trade_name' => 'Não Informado',
                'contacts' => [
                    [
                        'number_contact' => ltrim($number_contact, 0),
                        'type_contact' => 'H',
                    ]
                ]
            ];

        return $customerCheckout;
    }

    /**
     * Trata os dados do checkout do usuario logado
     *
     * @param $paymentData
     * @return array
     * @throws LocalizedException
     */
    public function getCheckoutUser($paymentData) {
        $order = $paymentData->getOrder();
        $street = $order->getBillingAddress()->getStreet();

        $objectManager = ObjectManager::getInstance();
        $customerRepository = $objectManager->get(CustomerRepositoryInterface::class);
        $customer = $customerRepository->getById($order->getCustomerId());

        $objectManager = ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');

        $checkCpfAndCnpj = $this->checkCpfAndCnpj($customerSession->getCustomer()->getData('cpf'), $customerSession->getCustomer()->getData('cnpj'));

        $number_contact = str_replace(" ","",$customer->getCustomAttribute(self::ATTR_PHONE_KEY)->getValue());
        $number_contact = str_replace("(","",$number_contact);
        $number_contact = str_replace(")","",$number_contact);
        $number_contact = str_replace("-","",$number_contact);

        $customerCheckout =
            [
                'name' => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
                'email' => $order->getCustomerEmail(),
                'cpf' =>  $checkCpfAndCnpj[0],
                'cnpj' => $checkCpfAndCnpj[1] ?? "",
                'company_name' => 'Não Informado',
                'trade_name' => 'Não Informado',
                'contacts' => [
                    [
                        'number_contact' => ltrim($number_contact, 0),
                        'type_contact' => $this->getTypeContact($customer->getCustomAttribute(self::ATTR_PHONE_KEY)->getValue()),
                    ]
                ]
            ];

        return $customerCheckout;
    }

    function checkStates($stateName)
    {
        $brazilianStates = array(
            'AC'=>'Acre',
            'AL'=>'Alagoas',
            'AP'=>'Amapá',
            'AM'=>'Amazonas',
            'BA'=>'Bahia',
            'CE'=>'Ceará',
            'DF'=>'Distrito Federal',
            'ES'=>'Espírito Santo',
            'GO'=>'Goiás',
            'MA'=>'Maranhão',
            'MT'=>'Mato Grosso',
            'MS'=>'Mato Grosso do Sul',
            'MG'=>'Minas Gerais',
            'PA'=>'Pará',
            'PB'=>'Paraíba',
            'PR'=>'Paraná',
            'PE'=>'Pernambuco',
            'PI'=>'Piauí',
            'RJ'=>'Rio de Janeiro',
            'RN'=>'Rio Grande do Norte',
            'RS'=>'Rio Grande do Sul',
            'RO'=>'Rondônia',
            'RR'=>'Roraima',
            'SC'=>'Santa Catarina',
            'SP'=>'São Paulo',
            'SE'=>'Sergipe',
            'TO'=>'Tocantins'
        );
        $result = array_search($stateName, $brazilianStates);
        return $result;
    }

    public function getCheckoutCompany($customerCheckout, $billingAddress) {
        if($customerCheckout['cnpj'] != '' || $customerCheckout['cnpj'] != null) {
            return [
                'cnpj'  => $customerCheckout['cnpj'],
                'company_name' => $billingAddress->getCompany(),
                'trade_name' => $billingAddress->getCompany(),
            ];
        }
        return false;
    }

    /**
     * Gera os dados do cliente
     *
     * @param Order $order
     * @return array
     */
    function generateCustomerData($paymentData)
    {
        $order = $paymentData->getOrder();

        $street = $order->getBillingAddress()->getStreet();
        $objectManager = ObjectManager::getInstance();

        $state = $this->checkStates($order->getBillingAddress()->getRegion());

        if($order->getCustomerId() != null) {
            $customerCheckout = $this->getCheckoutUser($paymentData);
        }
        else {
            $customerCheckout = $this->getCheckoutVisitant($paymentData);
        }

        $number_contact = str_replace(" ","",$customerCheckout['contacts'][0]['number_contact']);
        $number_contact = str_replace("(","",$number_contact);
        $number_contact = str_replace(")","",$number_contact);
        $number_contact = str_replace("-","",$number_contact);

        $customerData =  [
            'name'  => $customerCheckout['name'],
            'email' => $customerCheckout['email'],
            'cpf'   => $customerCheckout['cpf'],
            'contacts' => [
                [
                    'number_contact' => ltrim($number_contact, 0),
                    'type_contact' => $customerCheckout['contacts'][0]['type_contact'],
                ]
            ],
            'addresses' => [
                [
                    'type_address' => self::TYPE_ADDRESS,
                    'street' => $street[0],
                    'number' => $street[1] ?? 'Não informado',
                    'city' => $order->getBillingAddress()->getCity(),
                    'state' => $order->getBillingAddress()->getRegion(),
                    // 'neighborhood' => $paymentData->getData('additional_information')['neighborhoodCustomer'],
                    'state' => $state,
                    'completion' => $street[2] ?? 'Não informado',
                    'neighborhood' => $street[3] ?? 'Não informado',
                    'postal_code' => $order->getBillingAddress()->getPostcode()
                ]
            ]
        ];

        if ($this->getCheckoutCompany($customerCheckout, $order->getBillingAddress()) != false) {
            return array_merge($customerData, $this->getCheckoutCompany($customerCheckout, $order->getBillingAddress()));
        }

        return $customerData;
    }

    /**
     * Método gera a transação no Yapay
     *
     * @param array $paymentData
     * @return mixed
     */
    public function generateTransaction($paymentData)
    {
        $order = $paymentData->getOrder();
        $orderIncrementId = $order->getData('increment_id');
        $prefixoPedidoY = $this->getPrefixo();
        $payment = [];
        $payment["token_account"] = $this->getToken();
        $payment['customer'] = $this->generateCustomerData($paymentData);
        $payment["transaction_product"] = [];
        $items = $this->_cart->getItems()->getData();
        // $item = [];

        //Filtra a var $items removendoo que é product_type => configurable
        foreach($items as $key => $value){
            $found = array_search('configurable',$value);
            if($found != false AND $found == 'product_type'){
                unset($items[$key]);
            }
        }

        foreach ($items as $key => $item) {

            $payment["transaction_product"][$key]["description"] = $item["name"];
            $payment["transaction_product"][$key]["quantity"] = $item["qty"];
            $payment["transaction_product"][$key]["price_unit"] = $item["price"];
            $payment["transaction_product"][$key]["code"] = $item["product_id"];
            $payment["transaction_product"][$key]["sku_code"] = $item["sku"];
            $payment["transaction_product"][$key]["extra"] = $item["description"];
        }

        $payment["transaction"] = [];
        $payment["transaction"]["customer_ip"] = $order["remote_ip"];

        //order_number inicio

        // limita a 20 caracteres
        $payment["transaction"]["order_number"] = mb_strimwidth($prefixoPedidoY.$orderIncrementId, 0, 20);

        //order_number fim

        if (isset($order["shipping_description"])) {
            $payment["transaction"]["shipping_type"] = $order["shipping_description"];
            $payment["transaction"]["shipping_price"] = $order["shipping_amount"];
        }

        $discount = str_replace("-", "",$order->getDiscountAmount());

        if ($discount > 0) {
            $payment["transaction"]["price_discount"] = $discount;
        }

        $paymentInfo = $paymentData->getData('additional_information');

        // \Magento\Framework\App\ObjectManager::getInstance()
        // ->get('Psr\Log\LoggerInterface')
        // ->debug( $TotalOrder  );

        if ($paymentData->getData('method') == 'yapay_credit_card') {
            $parcelas = $this->getParcelas();
            $parcelaCheckout = $paymentInfo["cc_installments"];
            $TotalOrder = $order->getGrandTotal();

            if ($parcelas[$parcelaCheckout] > "0.0") {
                $totalJuros = ( floatval($TotalOrder) *  (floatval($parcelas[$parcelaCheckout]) / 100) );

                $payment["transaction"]["price_additional"] = $totalJuros;
            }
        }

        $payment["transaction"]["url_notification"] = $this->_getUrl('/').'yapay/notification/capture';
        $payment["transaction"]["free"] = "MAGENTO_2_API_v1.1.2";
        // $payment["transaction"]["free"] = "MAGENTO_2_API_v" . $this->getVersionModule();



        if ($paymentData->getData('method') == 'yapay_bank_slip') {

            $payment["payment"]["payment_method_id"] = self::PAYMENT_METHOD_BILLET;

        } else if ($paymentData->getData('method') == 'yapay_credit_card') {


            $payment["payment"]["payment_method_id"] = $paymentInfo["cc_card"];
            $payment["payment"]["card_name"] = $paymentInfo["cc_cardholder"];
            $payment["payment"]["card_number"] = $paymentInfo["cc_number"];
            $payment["payment"]["card_expdate_month"] = $paymentInfo["cc_exp_month"];
            $payment["payment"]["card_expdate_year"] = $paymentInfo["cc_exp_year"];
            $payment["payment"]["card_cvv"] = $paymentInfo["cc_security_code"];

            if ($paymentInfo["cc_card"] != '19') {
                $payment["payment"]["split"] = $paymentInfo["cc_installments"];
            }

        } else {
            $payment["payment"]["payment_method_id"] = $paymentInfo["payment_method_id"];;
        }

        $objectManager = ObjectManager::getInstance();

        $paymentApi = $objectManager->get(PaymentApi::class);

        $response = json_decode($paymentApi->generatePayment($payment, $this->getBaseURL()));


        // \Magento\Framework\App\ObjectManager::getInstance()
        // ->get('Psr\Log\LoggerInterface')
        // ->debug(json_encode( $payment ));

        \Magento\Framework\App\ObjectManager::getInstance()
        ->get('Psr\Log\LoggerInterface')
        ->debug(json_encode( $response ));


        $paymentData->setAdditionalInformation("url_payment", $response->data_response->transaction->payment->url_payment);
        $paymentData->setAdditionalInformation("linha_digitavel", $response->data_response->transaction->payment->linha_digitavel);
        $paymentData->setAdditionalInformation("boleto_url", $response->data_response->transaction->payment->url_payment);
        $paymentData->setTransactionId(
            $response->data_response->transaction->order_number
        )->setIsTransactionClosed(0);

        \Magento\Framework\App\ObjectManager::getInstance()
        ->get('Psr\Log\LoggerInterface')
        ->debug(json_encode( $response ));

        if ($paymentData->getData('method') == 'yapay_credit_card') {
            if ($paymentData->getAdditionalInformation("cc_number")) {
                $cc_numberMasked = $this->ccMasking($paymentData->getAdditionalInformation("cc_number"));
                $paymentData->setAdditionalInformation("cc_number", $cc_numberMasked);
            }

            if ($paymentData->getAdditionalInformation("cc_security_code"))  {
                $cvvMasked = "XXX";
                $paymentData->setAdditionalInformation("cc_security_code", $cvvMasked);
            }
        }


        $paymentData->update();

        return $paymentData;
    }

    /**
     * Método retorna cartão mascarado
     */
    protected function ccMasking($number, $maskingCharacter = 'X'){
        return substr($number, 0, 4) . str_repeat($maskingCharacter, strlen($number) - 8) . substr($number, -4);
    }

    /**
     * Método retorna o cliente http
     *
     * @return Client
     */
    protected function getClient()
    {
        return new Client(['base_uri' => $this->getBaseURL()]);
    }

    /**
     * Busca o ambiente de desenvolvimento na configuração da plataforma
     *
     * @return mixed
     */
    public function getEnvironment()
    {
        $environment = $this->_scopeConfig->getValue('payment/yapay_configuration/environment_configuration_yapay', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $environment;
    }

    /**
     * Busca token do lojista na configuração da plataforma
     *
     * @return mixed
     */
    public function getToken()
    {
        $token = $this->_scopeConfig->getValue('payment/yapay_configuration/token_configuration_yapay', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $token;
    }

    /**
     * Busca prefixo do pedido na configuração da plataforma
     *
     * @return mixed
     */
    public function getPrefixo()
    {
        $prefixoPedido = $this->_scopeConfig->getValue('payment/yapay_configuration/prefixoPedido', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $prefixoPedido;
    }

    public function getVersionModule()
    {
        $version = $this->_scopeConfig->getValue('modules/Yapay_Magento2/version', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $version;
    }

    /**
     * Busca token do lojista na configuração da plataforma
     *
     * @return mixed
     */
    public function getParcelas()
    {
        $parcel_interest_1 = $this->_scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_1', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $parcel_interest_2 = $this->_scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_2', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $parcel_interest_3 = $this->_scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_3', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $parcel_interest_4 = $this->_scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_4', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $parcel_interest_5 = $this->_scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_5', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $parcel_interest_6 = $this->_scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_6', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $parcel_interest_7 = $this->_scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_7', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $parcel_interest_8 = $this->_scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_8', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $parcel_interest_9 = $this->_scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_9', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $parcel_interest_10 = $this->_scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_10', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $parcel_interest_11 = $this->_scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_11', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $parcel_interest_12 = $this->_scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_12', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $parcela = array("1"  => $parcel_interest_1,
                         "2"  => $parcel_interest_2,
                         "3"  => $parcel_interest_3,
                         "4"  => $parcel_interest_4,
                         "5"  => $parcel_interest_5,
                         "6"  => $parcel_interest_6,
                         "7"  => $parcel_interest_7,
                         "8"  => $parcel_interest_8,
                         "9"  => $parcel_interest_9,
                         "10" => $parcel_interest_10,
                         "11" => $parcel_interest_11,
                         "12" =>  $parcel_interest_12);


        return $parcela;
    }

    /**
     * Método retorna o tipo de telefone informado no cadastro
     *
     * @param $phone
     * @return string
     * @see http://dev.yapay.com.br/intermediador/apis/ (Tabela 1)
     */
    protected function getTypeContact($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (strlen($phone) == 10) {
            return 'H';
        }

        return 'M';
    }

    /**
     * Método retorna o CPF ou CNPJ informado no cadastro
     *
     * @param $value
     * @param $expected
     * @return null|string|string[]
     */
    protected function getCnpjOrCpf($value, $expected)
    {
        $value  = preg_replace('/\D/', '', $value);

        if(strlen($value) > 13) {
            return $expected == 'cnpj' ? $value : null;
        }

        return $expected == 'cpf' ? $value : null;
    }
}
