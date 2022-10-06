<?php
/**
 * Copyright © Lyra Network.
 * This file is part of Mi Cuenta Web plugin for Magento 2. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Lyranetwork\Micuentaweb\Block\Customer;

use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\View\Element\Template\Context;

class Tab extends \Magento\Customer\Block\Account\SortLink
{
    /**
     * @var \Lyranetwork\Micuentaweb\Helper\Data
     */
    protected $dataHelper;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param DefaultPathInterface $defaultPath
     * @param \Lyranetwork\Micuentaweb\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        DefaultPathInterface $defaultPath,
        \Lyranetwork\Micuentaweb\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;

        parent::__construct($context, $defaultPath, $data);
    }

    /**
     * Render block HTML.
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (! $this->dataHelper->isOneClickActive()) {
            return '';
        }

        return parent::_toHtml();
    }
}
