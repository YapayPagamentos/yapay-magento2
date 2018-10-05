<?php

namespace Yapay\Magento2\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Asset\Source;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Model\CcConfig;


class BankSlipProvider implements ConfigProviderInterface
{

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;


    /**
     * BankSlipProvider constructor.
     * @param CcConfig $ccConfig
     * @param Source $assetSource
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CcConfig $ccConfig,
        Source $assetSource,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->ccConfig = $ccConfig;
        $this->assetSource = $assetSource;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * indica qual tipo de pagamento corresponde a classe
     * @var string[]
     */
    protected $_methodCode = 'yapay_bank_slip';

    /**
     * Realiza a busca das configurações do painel do lojista
     *
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return [
            'payment' => [
                'yapay_bank_slip' => [
                    'option' => 'BankSlip'
                ]
            ]
        ];
    }

}