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

class Other extends Micuentaweb
{
    protected $_template = 'Lyranetwork_Micuentaweb::payment/form/other.phtml';

    public function regroupPaymentMeans()
    {
        return $this->getConfigData('regroup_payment_means');
    }

    public function getAvailableCcTypes()
    {
        return $this->getMethod()->getAvailableCcTypes();
    }

    public function getAvailableMeans()
    {
        $amount = $this->getMethod()
            ->getInfoInstance()
            ->getQuote()
            ->getBaseGrandTotal();
        return $this->getMethod()->getAvailableMeans($amount);
    }
}
