<?php
/**
 * Created by PhpStorm.
 * User: matheus
 * Date: 27/08/18
 * Time: 16:18
 */

namespace Yapay\Magento2\Api;

use Yapay\Magento2\Helper\YapayData;
use Magento\Framework\App\ObjectManager;

class PaymentApi
{
    /**
     * Envia dados da transação para api do yapay
     * @param $payment
     * @return mixed
     */
    public function generatePayment($payment)
    {
        $data_string = json_encode($payment);

        $ch = curl_init('https://api.intermediador.sandbox.yapay.com.br/api/v3/transactions/payment');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payment));


        $result = curl_exec($ch);

        return $result;
    }

    /**
     * Consulta transação
     * @param $transactionToken
     * @return mixed
     */
    public function getTransactionByTransactionToken($transactionToken) {

        $objectManager = ObjectManager::getInstance();

        $helper = $objectManager->get(YapayData::class);

        $url = 'https://api.intermediador.sandbox.yapay.com.br/api/v3/transactions/get_by_token?token_account='.$helper->getToken().'&token_transaction='.$transactionToken.'';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);


        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );

        $result = curl_exec($ch);

        return $result;
    }


}