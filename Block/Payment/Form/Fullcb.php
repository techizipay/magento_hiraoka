<?php
/**
 * Copyright © Lyra Network.
 * This file is part of Mi Cuenta Web plugin for Magento 2. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Lyranetwork\Micuentaweb\Block\Payment\Form;

class Fullcb extends Micuentaweb
{
    protected $_template = 'Lyranetwork_Micuentaweb::payment/form/fullcb.phtml';

    public function getAvailableOptions()
    {
        if (! $this->getConfigData('enable_payment_options')) {
            // Local payment options selection is not allowed.
            return [];
        }

        $amount = $this->getMethod()
            ->getInfoInstance()
            ->getQuote()
            ->getBaseGrandTotal();
        return $this->getMethod()->getAvailableOptions($amount);
    }
}
