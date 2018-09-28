<?php
/**
 * Created by PhpStorm.
 * User: matheus
 * Date: 17/08/18
 * Time: 14:26
 */

namespace Yapay\Magento2\Model\Plugin\Checkout;

use Magento\Customer\Api\CustomerRepositoryInterface;

class LayoutProcessor
{
    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $customerSession = $objectManager->get('Magento\Customer\Model\Session');

        if($customerSession->getCustomer()->getId() == null) {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['neighborhood_yapay'] = [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => 'shippingAddress.custom_attributes',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input',
                    'options' => [],
                    'id' => 'neighborhood-yapay'
                ],
                'dataScope' => 'shippingAddress.custom_attributes.neighborhood_yapay',
                'label' => 'Bairro',
                'provider' => 'checkoutProvider',
                'visible' => true,
                'validation' => ['require' => true],
                'sortOrder' => 95,
                'id' => 'neighborhood-yapay'
            ];
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['cpf_customer'] = [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => 'shippingAddress.custom_attributes',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input',
                    'options' => [],
                    'id' => 'cpf-customer'
                ],
                'dataScope' => 'shippingAddress.custom_attributes.cpf_customer',
                'label' => 'CPF',
                'provider' => 'checkoutProvider',
                'visible' => true,
                'validation' => ['require' => true],
                'sortOrder' => 250,
                'id' => 'cpf-customer'
            ];
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['cnpj_customer'] = [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => 'shippingAddress.custom_attributes',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input',
                    'options' => [],
                    'id' => 'cnpj-customer'
                ],
                'dataScope' => 'shippingAddress.custom_attributes.cnpj_customer',
                'label' => 'CNPJ',
                'provider' => 'checkoutProvider',
                'visible' => true,
                'validation' => [],
                'sortOrder' => 500,
                'id' => 'cnpj-customer'
            ];
        }
        return $jsLayout;
    }
}