<?php
/**
 * Copyright © Lyra Network.
 * This file is part of Mi Cuenta Web plugin for Magento 2. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Lyranetwork\Micuentaweb\Model\System\Config\Source;

class GiftCard implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(
        \Lyranetwork\Micuentaweb\Model\Method\Gift $method
    ) {
        $this->method = $method;
    }

    public function toOptionArray()
    {
        $options =  [];

        foreach ($this->method->getSupportedCcTypes() as $code => $name) {
            $options[] = [
                'value' => $code,
                'label' => $code . ' - ' . $name
            ];
        }

        return $options;
    }
}
