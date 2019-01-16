<?php

namespace Yapay\Magento2\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class TefPaymentMethods implements ArrayInterface
{

    /**
     * Retorna opções de transferencia disponiveis
     *
     * @return array
     */
    public function toOptionArray()
    {
        $peela = json_encode(["14" =>"Peela"]);
        $itauShopline = json_encode(["7" =>"Itaú Shopline (Transferência)"]);
        $bradesco = json_encode(["22" =>"Transf. Online Bradesco"]);
        $bancoBrasil = json_encode(["23" =>"Transf. Online Banco do Brasil"]);
        return [
            ['value'=>$peela, 'paymentMethod'=>'Peela', 'label'=>'Peela'],
            ['value'=>$itauShopline, 'paymentMethod'=>'Itaú Shopline (Transferência)', 'label'=>'Itaú Shopline (Transferência)'],
            ['value'=>$bradesco, 'paymentMethod'=>'Transf. Online Bradesco', 'label'=>'Transf. Online Bradesco'],
            ['value'=>$bancoBrasil, 'paymentMethod'=>'Transf. Online Banco do Brasil', 'label'=>'Transf. Online Banco do Brasil']
        ];
    }
}