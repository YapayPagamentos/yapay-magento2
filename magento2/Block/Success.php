<?php

namespace Yapay\Magento2\Block;


/**
 * Class Success
 *
 * Apos a finalização do pagamento classe responsavel por exibir a tela de sucesso
 *
 * @package Yapay\Magento2\Block
 */
class Success extends \Magento\Checkout\Block\Onepage\Success
{

    /**
     * Chama a url do pagamento
     *
     * @return string[]
     */
    public function getUrlPayment()
    {
        return $this->_checkoutSession->getLastRealOrder()->getPayment()->getAdditionalInformation("url_payment");
    }

    /**
     * Busca o ultimo pedido
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_checkoutSession->getLastRealOrder();
    }

    /**
     * Busca dados do pagamento
     *
     * @return mixed
     */
    public function getPaymentMethod()
    {
        return $this->_checkoutSession->getLastRealOrder()->getPayment()->getData("method");
    }





}