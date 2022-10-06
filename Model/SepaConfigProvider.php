<?php
/**
 * Copyright © Lyra Network.
 * This file is part of Mi Cuenta Web plugin for Magento 2. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Lyranetwork\Micuentaweb\Model;

class SepaConfigProvider extends \Lyranetwork\Micuentaweb\Model\MicuentawebConfigProvider
{
    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Lyranetwork\Micuentaweb\Helper\Data $dataHelper
     * @param string $methodCode
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Lyranetwork\Micuentaweb\Helper\Data $dataHelper
    ) {
        parent::__construct(
            $storeManager,
            $urlBuilder,
            $dataHelper,
            \Lyranetwork\Micuentaweb\Helper\Data::METHOD_SEPA
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = parent::getConfig();

        $config['payment'][$this->method->getCode()]['oneClick'] = $this->method->isOneclickAvailable();

        $customer = $this->method->getCurrentCustomer();
        $maskedPan = '';
        if ($customer && $customer->getCustomAttribute('micuentaweb_sepa_iban_bic')) {
            $maskedPan = $customer->getCustomAttribute('micuentaweb_sepa_iban_bic')->getValue();
        }

        $config['payment'][$this->method->getCode()]['maskedPan'] = $this->renderMaskedPan($maskedPan);

        return $config;
    }
}
