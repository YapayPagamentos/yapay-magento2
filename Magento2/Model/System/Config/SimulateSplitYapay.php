<?php

namespace Yapay\Magento2\Model\System\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Config\DataInterface;
use Yapay\Magento2\Helper\YapayData;
use Yapay\Magento2\Api\PaymentApi;

/**
 * Class SimulateSplitYapay
 * @package Yapay\Magento2\Model\System\Config
 */
class SimulateSplitYapay implements ArrayInterface
{

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
    
    public function getBaseURL()
    {
        if ($this->getEnvironment() == 'production') {
            return self::URL_PRODUCTION;
        }
        return self::URL_SANDBOX;

        // \Magento\Framework\App\ObjectManager::getInstance()
        // ->get('Psr\Log\LoggerInterface')
        // ->debug("oi");
    }
     
    public function getEnvironment()
    {
        $objectManager = ObjectManager::getInstance();
        $scopeConfig = $objectManager->get(ScopeConfigInterface::class);
        $environment = $scopeConfig->getValue('payment/yapay_configuration/environment_configuration_yapay', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $environment;
    }
     
    public function __construct(Context $context)
    {
        $this->paymentApi = $context->getObjectManager()->get(PaymentApi::class);
        $this->helper = $context->getObjectManager()->get(YapayData::class);
    }


    public function getSplitYapay($totalOrder) {

        $result = $this->paymentApi->getSimulateSplitYapay($this->getBaseURL(), $totalOrder);


        return $result;
    }
    /**
     * Retorna o parcelamento da Yapay
     *
     * @return array
     */
    public function toOptionArray()
    {        
        return [
            // ['value'=>$apiYapay, 'label'=>$apiYapay],
        ];
        
    }
}