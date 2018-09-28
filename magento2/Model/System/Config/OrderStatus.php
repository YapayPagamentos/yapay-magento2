<?php
/**
 * Created by PhpStorm.
 * User: matheus
 * Date: 11/05/18
 * Time: 14:05
 */

namespace Yapay\Magento2\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Environment
 * @package UOL\PagSeguro\Model\System\Config
 */
class OrderStatus implements ArrayInterface
{
    /**
     * Constante de tipo de estado do pedido
     */
    const ACTIVE = "active";
    /**
     * Constante de tipo de estado do pedido
     */
    const PENDING = "pending";

    /**
     * Retorna array de opções de estados
     * @return array
     */
    public function toOptionArray()
    {
        return [
            self::ACTIVE => __('Active'),
            self::PENDING => __('Pending')
        ];
    }
}