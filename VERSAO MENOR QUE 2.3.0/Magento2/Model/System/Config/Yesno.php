<?php
/**
 * Created by PhpStorm.
 * User: matheus
 * Date: 11/05/18
 * Time: 14:09
 */

namespace Yapay\Magento2\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Yesno
 * @package Yapay\Magento2\Model\System\Config
 */
class Yesno implements ArrayInterface
{

    const YES  = 1;

    const NO = 0;

    /**
     * @return array of options
     */
    public function toOptionArray()
    {
        return [
            self::YES => __('Sim'),
            self::NO => __('Não')
        ];
    }
}
