<?php
/**
 * Copyright © Lyra Network.
 * This file is part of Mi Cuenta Web plugin for Magento 2. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Lyranetwork\Micuentaweb\Block\Adminhtml\System\Config\Form\Field;

/**
 * Custom renderer for the context mode element.
 */
class CtxMode extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Unset some non-related element parameters.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Get configured features.
        $features = \Lyranetwork\Micuentaweb\Helper\Data::$pluginFeatures;

        if ($features['qualif']) {
            $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
            $element->setDisabled(true);
        }

        return parent::render($element);
    }
}
