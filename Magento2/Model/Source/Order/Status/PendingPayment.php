<?php
namespace Yapay\Magento2\Model\Config\Source\Order\Status;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Config\Source\Order\Status;
/**
 * Order Status source model
 */
class PendingPayment extends Status
{
    /**
     * @var string[]
     */
    protected $_stateStatuses = [Order::STATE_PENDING_PAYMENT];

    public function toOptionArray()
    {
        return $this->_stateStatuses;
    }
}