<?php

namespace Yapay\Magento2\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Asset\Source;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Model\CcConfig;


class TransferenceProvider implements ConfigProviderInterface
{


    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;


    /**
     * @param CcConfig $ccConfig
     * @param Source $assetSource
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
     * Constante que indica qual tipo de pagamento corresponde a classe
     * @var string[]
     */
    protected $_methodCode = 'yapay_transference';

    /**
     * Retorna as configurações do painel do lojista
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                'yapay_transference' => [
                    'available_payment_transference_yapay' => $this->transferenceBanks()
                ]
            ]
        ];
    }

    /**
     * Busca os tipos de transferencias selecionadas na plataforma do lojista
     *
     * @return mixed
     */
    public function transferenceBanks()
    {
        return $this->scopeConfig->getValue('payment/yapay_transference/available_payment_transference_yapay');
    }



}