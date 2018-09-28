<?php

namespace Yapay\Magento2\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Asset\Source;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Model\CcConfig;

/**
 * Class CreditCardProvider
 * @package Yapay\Magento2\Model
 */
class CreditCardProvider implements ConfigProviderInterface
{


    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var array
     */
    private $icons = [];


    /**
     * CreditCardProvider constructor.
     * @param CcConfig $ccConfig
     * @param Source $assetSource
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Model\Context $context
     */
    public function __construct(
        CcConfig $ccConfig,
        Source $assetSource,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\Context $context
    ) {
        $this->ccConfig = $ccConfig;
        $this->assetSource = $assetSource;
        $this->scopeConfig = $scopeConfig;
        $this->_logger = $context->getLogger();;
    }

    /**
     * Constante que indica qual tipo de pagamento corresponde a classe
     * @var string[]
     */
    protected $_methodCode = 'yapay_credit_card';

    /**
     * Retorna as configurações do painel do lojista
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                'yapay_credit_card' => [
                    'availableTypes' =>  $this->getCcAvailableTypes(),
                    'months' => [$this->_methodCode => $this->ccConfig->getCcMonths()],
                    'years' => [$this->_methodCode => $this->ccConfig->getCcYears()],
                    'hasVerification' => $this->ccConfig->hasVerification(),
                    'installments' => $this->Installment(),
                    'cctypes' => $this->CcType(),
                    'icons' => $this->getIcons(),
                    'interestInstallments' => $this->interestInstallments()

                ]
            ]
        ];
    }

    /**
     * Busca a quantidade de parcela escolhida pelo lojista na plataforma
     *
     * @return mixed
     */
    public function Installment()
    {
        return $this->scopeConfig->getValue('payment/yapay_credit_card/installments');
    }

    /**
     * Busca os cartões selecionados pelo lojista na plataforma
     *
     * @return mixed
     */
    public function CcType()
    {
        return $this->scopeConfig->getValue('payment/yapay_credit_card/cctypes');
    }

    /**
     * Realiza tratamento dos cartões buscado no painel do lojista
     *
     * @return array
     */
    protected function getCcAvailableTypes()
    {
        $types = $this->CcType();
        $string_types_quebrada = explode(",", $types);
        $arrayJson =  [];
        foreach ($string_types_quebrada as $key => $value) {
            $arrayJson[] = json_decode($string_types_quebrada[$key]);
        }

        return $arrayJson;
    }

    /**
     * Retorna as urls das bandeiras dos cartões
     *
     * @return array
     */
    public function getIcons()
    {
        if (!empty($this->icons)) {
            return $this->icons;
        }

        $types = $this->CcType();
        $string_types_quebrada = explode(",", $types);
        $arrayJson =  [];
        foreach ($string_types_quebrada as $key => $value) {
            $arrayJson[] = json_decode($string_types_quebrada[$key]);
        }


        $codes = [];
        $item = [];
        foreach ($arrayJson as $key => $value ) {
            $codes[] = $arrayJson[$key];
            foreach ($arrayJson[$key] as $cardNumber => $cardName) {

                $item[] = $cardName;
                $asset = $this->ccConfig
                    ->createAsset('Yapay_Magento2::images/cc/' . strtolower($cardName) . '-flag.svg');
                $placeholder = $this->assetSource->findSource($asset);
                if ($placeholder) {
                    list($width, $height) = getimagesize($asset->getSourceFile());
                    $this->icons[strtolower($cardName)] = [
                        'url' => $asset->getUrl(),
                        'width' => $width,
                        'height' => $height
                    ];
                }
            }
        }
        return $this->icons;
    }

    /**
     * Busca os juros de cada parcela
     *
     * @return array
     */
    public function interestInstallments()
    {
        return [
            $this->scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_1'),
            $this->scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_2'),
            $this->scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_3'),
            $this->scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_4'),
            $this->scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_5'),
            $this->scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_6'),
            $this->scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_7'),
            $this->scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_8'),
            $this->scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_9'),
            $this->scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_10'),
            $this->scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_11'),
            $this->scopeConfig->getValue('payment/yapay_credit_card/parcel_interest_12')
        ];
    }


//    /**
//     * Calcula os juros de cada parcela
//     *
//     * @return array
//     */
//    public function calculatesInterest()
//    {
//
//        $objectManager = ObjectManager::getInstance();
//
//        $cart = $objectManager->get(Cart::class);
//
//
//        $totalOrder = $cart->getQuote()->getData('grand_total');
//
//        $plotsContainingInterest = [];
//        $interestInstallment = $this->interestInstallments();
//
//        for($i = 0; $i < $this->Installment(); $i++) {
//            $totalOrderInterest = number_format($totalOrder + ($totalOrder * floatval($interestInstallment[$i])) / 100, 2, ',', '.');
//            $totalInstallment = number_format(floatval($totalOrderInterest) / ($i +1),  2, ',', '.');
//            array_push($plotsContainingInterest, $i+1 . ' x R$' .$totalInstallment. ' Total à Pagar = R$' .$totalOrderInterest);
//        }
//
//        return $plotsContainingInterest;
//    }

}