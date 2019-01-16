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
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Phrase;

class PaymentApi
{
    /**
     * Envia dados da transação para api do yapay
     * @param $payment
     * @return mixed
     */
    public function generatePayment($payment, $url)
    {
        $data_string = json_encode($payment);

        $ch = curl_init($url.'/api/v3/transactions/payment');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payment));

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);


        if($code != 200 && $code != 201) {
            $error = json_decode($result);
            $error_message = [];

            if (isset($error->error_response->validation_errors)) {
                foreach ($error->error_response->validation_errors as $value) {
                    array_push($error_message, $value->message_complete);
                }
                throw new ValidatorException(new Phrase(implode(", ", $error_message)));
            }

            if (isset($error->error_response->general_errors)) {
                foreach ($error->error_response->general_errors as $value) {
                    array_push($error_message, $value->message);
                }
                throw new ValidatorException(new Phrase(implode(", ", $error_message)));
            }
        }

        return $result;
    }

    /**
     * Consulta transação
     * @param $transactionToken
     * @return mixed
     */
    public function getTransactionByTransactionToken($transactionToken, $url_environment)
    {
        $objectManager = ObjectManager::getInstance();
        $helper = $objectManager->get(YapayData::class);
        $url = $url_environment.'api/v3/transactions/get_by_token?token_account='.$helper->getToken().'&token_transaction='.$transactionToken.'';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );

        $result = curl_exec($ch);
        
        return $result;
    }
}