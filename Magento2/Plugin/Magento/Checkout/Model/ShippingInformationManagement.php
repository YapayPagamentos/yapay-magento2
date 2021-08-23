<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
//declare(strict_types=1);

namespace Yapay\Magento2\Plugin\Magento\Checkout\Model;
use \Magento\Checkout\Model\ConfigProviderInterface;
use Yapay\Magento2\Model\System\Config\SimulateSplitYapay;


class ShippingInformationManagement implements ConfigProviderInterface
{

    /**
     * @var SimulateSplitYapay
     */
    protected $SimulateSplitYapay;

    public function __construct(
        SimulateSplitYapay $SimulateSplitYapay,
        SimulateSplitYapay $SimulateSplitYapayy
    ) {
        $this->SimulateSplitYapayy = $SimulateSplitYapay;        
    }

    // /**
    //  * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
    //  * @param $cartId
    //  * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    //  */
    // public function beforeSaveAddressInformation(
    //     \Magento\Checkout\Model\ShippingInformationManagement $subject,
    //     $cartId,
    //     \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    // )
    // {
    //     \Magento\Framework\App\ObjectManager::getInstance()
    //     ->get('Psr\Log\LoggerInterface')
    //     ->debug("oi beforeSaveAddressInformation");        
    //     $parcelas = $this->getConfig(); 
    // }

    public function afterSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $shipping,
         $result
    )
    {

        $parcelas = $this->getConfig(); 
    }

    public function getConfig()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
        $totalOrder = $cart->getQuote()->getData('grand_total');
       

        $simulateSpit = $this->SimulateSplitYapayy->getSplitYapay(number_format((float)$totalOrder, 2, '.',''));

        // \Magento\Framework\App\ObjectManager::getInstance()
        // ->get('Psr\Log\LoggerInterface')
        // ->debug(json_encode($simulateSpit));  


        return [
            'payment' => [
                'yapay_credit_card' => [
                    'parcelamentoYapay' => json_encode($simulateSpit),
                ]
            ]
        ];
    }
}

