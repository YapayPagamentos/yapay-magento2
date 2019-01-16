<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Yapay\Magento2\Controller\Onepage;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Result\PageFactory;
use Magento\Checkout\Model\Session;

class Success extends \Magento\Checkout\Controller\Onepage
{
    /**
     * Order success action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $session = $this->getOnepage()->getCheckout();
        if (!$this->_objectManager->get(\Magento\Checkout\Model\Session\SuccessValidator::class)->isValid()) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
        $session->clearQuote();
        //@todo: Refactor it to match CQRS
        $resultPage = $this->resultPageFactory->create();
        $this->_eventManager->dispatch(
            'checkout_onepage_controller_success_action',
            ['order_ids' => [$session->getLastOrderId()]]
        );

        #Google Analytics
        $this->_eventManager->dispatch(
            'google_analytics',
            ['order_ids' => [$session->getLastOrderId()]]
        );
        
        return $resultPage;
    }
}
