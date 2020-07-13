<?php
/**
 * Created by PhpStorm.
 * User: jessica
 * Date: 26/07/18
 * Time: 10:53
 */

namespace Yapay\Magento2\Model\System\Message;


use Magento\Framework\Notification\MessageInterface;
use Magento\Framework\View\Asset\Source;
use Magento\Framework\App\Config\ScopeConfigInterface;


/**
 * Class CustomNotification
 */
class CustomSystemMessage implements MessageInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Psr\Log\LoggerInterface
     */

    public function __construct(
        Source $assetSource,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\Context $context
    ) {
        $this->assetSource = $assetSource;
        $this->scopeConfig = $scopeConfig;
        $this->_logger = $context->getLogger();

    }

    /**
     * Message identity
     */
    const MESSAGE_IDENTITY = 'yapay_system_message';

    /**
     * Retrieve unique system message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return self::MESSAGE_IDENTITY;
    }

    /**
     * Check whether the system message should be shown
     *
     * @return bool
     */
    public function isDisplayed()
    {

        $bank_slip = $this->scopeConfig->getValue('payment/yapay_bank_slip/active');
        $credit_card = $this->scopeConfig->getValue('payment/yapay_credit_card/active');
        $transference = $this->scopeConfig->getValue('payment/yapay_transference/active');
        if(!$bank_slip && !$credit_card && !$transference) {
            return true;
        }

    }

    /**
     * Retrieve system message text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getText()
    {
        return __('No Yapay payment method selected.');
    }

    /**
     * Retrieve system message severity
     * Possible default system message types:
     * - MessageInterface::SEVERITY_CRITICAL
     * - MessageInterface::SEVERITY_MAJOR
     * - MessageInterface::SEVERITY_MINOR
     * - MessageInterface::SEVERITY_NOTICE
     *
     * @return int
     */
    public function getSeverity()
    {
        return self::SEVERITY_MAJOR;
    }
}