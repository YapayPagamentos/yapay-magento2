<?php
namespace Yapay\Magento2\Model\System\Config;
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

    /**
     * Constante de tipo de estado do pedido
     */
    const PENDING = "pending_payment";
    public function toOptionArray()
    {
        return [
            self::PENDING => __('pending_payment')
        ];    }
}