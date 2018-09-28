<?php

namespace Yapay\Magento2\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

use Magento\Framework\Config\DataInterface;

/**
 * Class Paymentmethods
 * @package Yapay\Magento2\Model\System\Config
 */
class Paymentmethods implements ArrayInterface
{
    /**
     * Retorna bandeiras de cartões disponiveis
     *
     * @return array
     */
    public function toOptionArray()
    {


        $visa = json_encode(["3" =>"Visa"]);
        $mastercard = json_encode(["4" => "Mastercard"]);
        $dinners = json_encode(["2"=>"Diners"]);
        $americanExpress = json_encode(["5"=>"American Express"]);
        $alura = json_encode(["18"=>"Aura"]);
        $elo = json_encode(["16"=>"Elo"]);
        $discover = json_encode(["15"=>"Discover"]);
        $jcb = json_encode(["19"=>"JCB"]);
        $hipercard = json_encode(["20"=>"Hipercard"]);
        $hiper = json_encode(["25"=>"Hiper"]);

        return [
                ['value'=> $visa,  'card'=>'Visa',  'label'=>'Visa'],
                ['value'=> $mastercard, 'card'=>'Mastercard', 'label'=>'Mastercard'],
                ['value'=> $dinners, 'card'=>'Diners', 'label'=>'Diners'],
                ['value'=> $americanExpress, 'card'=>'American Express', 'label'=>'American Express'],
                ['value'=> $alura, 'card'=>'Aura', 'label'=>'Aura'],
                ['value'=> $elo, 'card'=>'Elo', 'label'=>'Elo'],
                ['value'=> $discover, 'card'=>'Discover', 'label'=>'Discover'],
                ['value'=> $jcb, 'card'=>'JCB', 'label'=>'JCB'],
                ['value'=> $hipercard, 'card'=>'Hipercard', 'label'=>'Hipercard'],
                ['value'=> $hiper, 'card'=>'Hiper', 'label'=>'Hiper']
        ];

    }
}