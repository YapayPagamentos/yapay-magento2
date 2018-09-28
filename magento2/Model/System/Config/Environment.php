<?php

namespace Yapay\Magento2\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Environment
 * @package Yapay\Magento\Model\System\Config
 */
class Environment implements ArrayInterface
{
    /**
     * Constante Sandbox
     */
    const SANDBOX = "sandbox";
    /**
     *  Constante Produção
     *
     */
    const PRODUCTION = "production";

    /**
     * Retorna opções de ambiente
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            self::SANDBOX => __('Sandbox'),
            self::PRODUCTION => __('Production')
        ];
    }
}