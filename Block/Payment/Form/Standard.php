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

class Standard extends Micuentaweb
{
    protected $_template = 'Lyranetwork_Micuentaweb::payment/form/standard.phtml';

    public function getAvailableCcTypes()
    {
        return $this->getMethod()->getAvailableCcTypes();
    }

    public function getCcTypeNetwork($code)
    {
        $cbCards = [
            'CB',
            'VISA',
            'VISA_ELECTRON',
            'MASTERCARD',
            'MAESTRO',
            'E-CARTEBLEUE',
            'VPAY'
        ];

        if ($code === 'AMEX') {
            return 'AMEX';
        } elseif (in_array($code, $cbCards)) {
            return 'CB';
        }

        return null;
    }

    public function isLocalCcType()
    {
        return $this->getMethod()->isLocalCcType();
    }

    // Check if the 1-click payment is active for Standard.
    public function isOneClickActive()
    {
        return $this->getMethod()->isOneClickActive();
    }
}
