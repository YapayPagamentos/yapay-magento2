<?php
namespace Yapay\Magento2\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class QtdSplit
 * @package Yapay\Magento2\Model\System\Config
 */
class QtdSplit implements ArrayInterface
{
    /**
     * Retorna opções de parcelamento disponivel
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value'=>'1', 'label'=>'1'],
            ['value'=>'2', 'label'=>'2'],
            ['value'=>'3', 'label'=>'3'],
            ['value'=>'4', 'label'=>'4'],
            ['value'=>'5', 'label'=>'5'],
            ['value'=>'6', 'label'=>'6'],
            ['value'=>'7', 'label'=>'7'],
            ['value'=>'8', 'label'=>'8'],
            ['value'=>'9', 'label'=>'9'],
            ['value'=>'10', 'label'=>'10'],
            ['value'=>'11', 'label'=>'11'],
            ['value'=>'12', 'label'=>'12']
        ];
    }
}