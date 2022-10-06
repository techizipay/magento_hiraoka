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

class OneyConfigProvider extends \Lyranetwork\Micuentaweb\Model\MicuentawebConfigProvider
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Lyranetwork\Micuentaweb\Helper\Data $dataHelper
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Lyranetwork\Micuentaweb\Helper\Data $dataHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper
    ) {
        parent::__construct(
            $storeManager,
            $urlBuilder,
            $dataHelper,
            \Lyranetwork\Micuentaweb\Helper\Data::METHOD_ONEY
        );

        $this->timezone = $timezone;
        $this->pricingHelper = $pricingHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = parent::getConfig();
        $config['payment'][$this->method->getCode()]['availableOptions'] = $this->getAvailableOptions();

        return $config;
    }

    private function getAvailableOptions()
    {
        $quote = $this->dataHelper->getCheckoutQuote();
        $amount = ($quote && $quote->getId()) ? $quote->getBaseGrandTotal() : null;

        $options = [];
        foreach ($this->method->getAvailableOptions($amount) as $key => $option) {
            // Option will be available.
            $c = is_numeric($option['count']) ? $option['count'] : 1;
            $r = is_numeric($option['rate']) ? $option['rate'] : 0;

            // Get final option description.
            $search = array('%c', '%r');
            $replace = array($c, $r . ' %');
            $label = str_replace($search, $replace, $option['label']); // Label to display on payment page.

            $options[] = [
                'key' => $key,
                'label' => $label,
            ];
        }

        return $options;
    }
}
