<?php
/**
 * Copyright © Lyra Network.
 * This file is part of Mi Cuenta Web plugin for Magento 2. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Lyranetwork\Micuentaweb\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class UpdateMultiPaymentObserver implements ObserverInterface
{
    /**
     * Update payment method ID to set installments number if multi payment.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $payment = $observer->getDataObject();

        if ($payment->getMethod() !== 'micuentaweb_multi') {
            // Not micuentaweb multiple payment, do nothing.
            return $this;
        }

        // Retreive selected option.
        $option = @unserialize($payment->getAdditionalInformation(\Lyranetwork\Micuentaweb\Helper\Payment::MULTI_OPTION));
        if (is_array($option) && ! empty($option)) {
            $payment->setMethod('micuentaweb_multi_' . $option['count'] . 'x');
        }

        return $this;
    }
}
