<?php
namespace Yapay\Magento2\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class ValorMinimoParc
 * @package Yapay\Magento2\Model\System\Config
 */
class ValorMinimoParc implements ArrayInterface
{
    /**
     * Retorna opções de parcelamento disponivel
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            // ['value'=>'1', 'label'=>'1']
        ];
    }
}