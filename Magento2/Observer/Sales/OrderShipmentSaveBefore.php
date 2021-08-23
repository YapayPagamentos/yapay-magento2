<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Yapay\Magento2\Observer\Sales;

class OrderShipmentSaveBefore implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        \Magento\Framework\App\ObjectManager::getInstance()
        ->get('Psr\Log\LoggerInterface')
        ->debug("oi OrderShipmentSaveBefore");    
    }
}
