<?php
/**
 * Copyright © Lyra Network.
 * This file is part of Mi Cuenta Web plugin for Magento 2. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Lyranetwork\Micuentaweb\Helper;

use Lyranetwork\Micuentaweb\Model\Api\MicuentawebApi;
use Magento\Framework\Exception\NoSuchEntityException;

class Checkout
{
    const ORDER_ID_REGEX = '#^[a-zA-Z0-9]{1,9}$#';

    const CUST_ID_REGEX = '#^[a-zA-Z0-9]{1,8}$#';

    const PRODUCT_REF_REGEX = '#^[a-zA-Z0-9]{1,64}$#';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Lyranetwork\Micuentaweb\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $shippingConfig;

    /**
    * @var \Magento\Framework\Stdlib\CookieManagerInterface
    */
    protected $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Lyranetwork\Micuentaweb\Helper\Data $dataHelper
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Lyranetwork\Micuentaweb\Helper\Data $dataHelper,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->categoryRepository = $categoryRepository;
        $this->eavConfig = $eavConfig;
        $this->storeManager = $storeManager;
        $this->dataHelper = $dataHelper;
        $this->shippingConfig = $shippingConfig;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    public function checkCustormers($scope, $scopeId)
    {
        // Check customer IDs.
        $collection = $this->customerCollectionFactory->create();

        if ($scope === 'websites') {
            $collection->addAttributeToFilter('website_id', $scopeId);
        } elseif ($scope === 'stores') {
            $collection->addAttributeToFilter('store_id', $scopeId);
        }

        $collection->load();

        foreach ($collection as $customer) {
            if (! preg_match(self::CUST_ID_REGEX, $customer->getId())) {
                // A customer ID doesn't match gateway rules.
                $msg = '';
                $msg .= __(
                    'Customer ID &laquo; %1 &raquo; does not match Mi Cuenta Web specifications.',
                    $customer->getId()
                )->render() . ' ';
                $msg .= __('This field must agree to the regular expression %1.', self::CUST_ID_REGEX)->render();

                throw new \Magento\Framework\Exception\LocalizedException(__($msg));
            }
        }
    }

    public function checkOrders($scope, $scopeId)
    {
        // Check order IDs.
        if ($scope === 'stores') {
            // Store context.
            $incrementId = $this->eavConfig->getEntityType(\Magento\Sales\Model\Order::ENTITY)->fetchNewIncrementId(
                $scopeId
            );

            $this->checkOrderId($incrementId);
        } else {
            // General and website context.
            $stores = $this->storeManager->getStores();

            foreach ($stores as $store) {
                if (($scope === 'websites') && ($store->getWebsiteId() != $scopeId)) {
                    continue;
                }

                $incrementId = $this->eavConfig->getEntityType(\Magento\Sales\Model\Order::ENTITY)->fetchNewIncrementId(
                    $store->getId()
                );
                $this->checkOrderId($incrementId);
            }
        }
    }

    private function checkOrderId($orderId)
    {
        if (! preg_match(self::ORDER_ID_REGEX, $orderId)) {
            // The potential next order id doesn't match gateway rules.
            $msg = '';
            $msg .= __(
                'The next order ID  &laquo; %1 &raquo; does not match Mi Cuenta Web specifications.',
                $orderId
            )->render() . ' ';
            $msg .= __('This field must agree to the regular expression %1.', self::ORDER_ID_REGEX)->render();

            throw new \Magento\Framework\Exception\LocalizedException(__($msg));
        }
    }

    public function checkProducts($scope, $scopeId)
    {
        // Check products' IDs and labels.
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('name');

        if ($scope === 'websites') {
            $collection->addWebsiteFilter($scopeId);
        } elseif ($scope === 'stores') {
            $collection->addStoreFilter($scopeId);
        }

        $collection->load();

        foreach ($collection as $product) {
            if (! preg_match(self::PRODUCT_REF_REGEX, $product->getId())) {
                // Product id doesn't match gateway rules.
                $msg = '';
                $msg .= __(
                    'Product reference &laquo; %1 &raquo; does not match Mi Cuenta Web specifications.',
                    $product->getId()
                )->render() . ' ';
                $msg .= __('This field must agree to the regular expression %1.', self::PRODUCT_REF_REGEX)->render();

                throw new \Magento\Framework\Exception\LocalizedException(__($msg));
            }
        }
    }

    public function checkOneyRequirements($scope, $scopeId)
    {
        $this->checkOrders($scope, $scopeId);
        $this->checkCustormers($scope, $scopeId);
        $this->checkProducts($scope, $scopeId);
    }

    public function toMicuentawebCarrier($methodCode)
    {
        $shippingMapping = $this->dataHelper->unserialize($this->dataHelper->getCommonConfigData('ship_options'));

        if (is_array($shippingMapping) && ! empty($shippingMapping)) {
            foreach ($shippingMapping as $shippingMethod) {
                if ($shippingMethod['code'] === $methodCode) {
                    return $shippingMethod;
                }
            }
        }

        return null;
    }

    public function toMicuentawebCategory($categoryIds)
    {
        // Commmon category if any.
        $commonCategory = $this->dataHelper->getCommonConfigData('common_category');
        if ($commonCategory !== 'CUSTOM_MAPPING') {
            return $commonCategory;
        }

        $categoryMapping = $this->dataHelper->unserialize($this->dataHelper->getCommonConfigData('category_mapping'));

        if (is_array($categoryMapping) && ! empty($categoryMapping) && is_array($categoryIds) && ! empty($categoryIds)) {
            foreach ($categoryMapping as $category) {
                if (in_array($category['code'], $categoryIds)) {
                    return $category['micuentaweb_category'];
                }
            }
        }

        return null;
    }

    public function setCartData($order, &$micuentawebRequest)
    {
        $notAllowed = '#[^A-Z0-9ÁÀÂÄÉÈÊËÍÌÎÏÓÒÔÖÚÙÛÜÇ ]#ui';

        // Used currency.
        $currency = MicuentawebApi::findCurrencyByNumCode($micuentawebRequest->get('currency'));

        $subtotal = 0;

        // Load all products in the shopping cart.
        foreach ($order->getAllItems() as $item) {
            // Check to avoid sending the whole hierarchy of a configurable product.
            if (! $item->getParentItem()) {
                $product = $item->getProduct();

                $label = $item->getName();

                // Concat product label with one or two of its category names to make it clearer.
                $categoryIds = $product->getCategoryIds();
                if (is_array($categoryIds) && ! empty($categoryIds)) {
                    try {
                        if (isset($categoryIds[1]) && $categoryIds[1]) {
                            $category = $this->categoryRepository->get($categoryIds[1]);
                            $label = $category->getName() . ' I ' . $label;
                        }

                        if ($categoryIds[0]) {
                            $category = $this->categoryRepository->get($categoryIds[0]);
                            $label = $category->getName() . ' I ' . $label;
                        }
                    } catch (NoSuchEntityException $e) {
                        $this->dataHelper->log(
                            "Exception while retrieving product category with code {$e->getCode()}: {$e->getMessage()}",
                            \Psr\Log\LogLevel::WARNING
                        );
                    }
                }

                $priceInCents = $currency->convertAmountToInteger($item->getPrice());
                $qty = (int) $item->getQtyOrdered();

                $micuentawebRequest->addProduct(
                    preg_replace($notAllowed, ' ', $label),
                    $priceInCents,
                    $qty,
                    $item->getProductId(),
                    $this->toMicuentawebCategory($categoryIds)
                );

                $subtotal += $priceInCents * $qty;
            }
        }

        $micuentawebRequest->set('insurance_amount', 0); // By default, shipping insurance amount is not available in Magento.
        $micuentawebRequest->set('shipping_amount', $currency->convertAmountToInteger($order->getShippingAmount()));

        // Recalculate tax_amount to avoid rounding problems.
        $taxAmount = $micuentawebRequest->get('amount') - $subtotal - $micuentawebRequest->get('shipping_amount') -
             $micuentawebRequest->get('insurance_amount');
        if ($taxAmount <= 0) { // When order is discounted.
            $taxAmount = $currency->convertAmountToInteger($order->getTaxAmount());
        }

        $micuentawebRequest->set('tax_amount', $taxAmount);

        // VAT amount for colombian payment means.
        $micuentawebRequest->set('totalamount_vat', $taxAmount);
    }

    public function setAdditionalShippingData($order, &$micuentawebRequest)
    {
        // By default, Magento customers are private.
        $micuentawebRequest->set('cust_status', 'PRIVATE');
        $micuentawebRequest->set('ship_to_status', 'PRIVATE');

        // If this is Oney 3x/4x.
        $useOney = $micuentawebRequest->get('payment_cards') === 'ONEY_3X_4X';

        $notAllowedCharsRegex = '#[^A-Z0-9ÁÀÂÄÉÈÊËÍÌÎÏÓÒÔÖÚÙÛÜÇ -]#ui';

        if ($order->getIsVirtual() || ! $order->getShippingMethod()) { // There is no shipping method.
            $micuentawebRequest->set('ship_to_type', 'ETICKET');
            $micuentawebRequest->set('ship_to_speed', 'EXPRESS');
        } else {
            $shippingMethod = $this->toMicuentawebCarrier($order->getShippingMethod());

            if (! $shippingMethod) {
                $this->dataHelper->log(
                    'Cannot get mapped data for the order shipping method: ' . $order->getShippingMethod(),
                    \Psr\Log\LogLevel::WARNING
                );
                return;
            }

            // Get carrier name.
            $carriers = $this->shippingConfig->getAllCarriers($order->getStore()->getId());
            $carrierCode = substr($shippingMethod['code'], 0, strpos($shippingMethod['code'], '_'));
            $carrierName = $carriers[$carrierCode]->getConfigData('title');

            // Delivery point name.
            $name = '';
            switch ($shippingMethod['type']) {
                case 'RELAY_POINT':
                case 'RECLAIM_IN_STATION':
                case 'RECLAIM_IN_SHOP':
                    $name = $order->getShippingDescription();

                    if ($carrierName) {
                        $name = str_replace($carrierName . ' - ', '', $name);
                    }

                    if (strpos($name, '<')) {
                        $name = substr($name, 0, strpos($name, '<')); // Remove HTML elements.
                    }

                    // Modify address to send it to Oney server.
                    $address = $order->getShippingAddress()->getStreetLine(1);
                    $address .= $order->getShippingAddress()->getStreetLine(2) ? ' ' . $order->getShippingAddress()->getStreetLine(2) : '';

                    $micuentawebRequest->set('ship_to_street', $address);
                    $micuentawebRequest->set('ship_to_street2', $name);

                    if (empty($name)) {
                        $name = substr($order->getShippingDescription(), 0, 55);
                    }

                    // Send delivery point name, address, postcode and city in field ship_to_delivery_company_name.
                    $name .= ' ' . $address;
                    $name .= ' ' . $order->getShippingAddress()->getPostcode();
                    $name .= ' ' . $order->getShippingAddress()->getCity();

                    // Delete not allowed chars.
                    $micuentawebRequest->set('ship_to_delivery_company_name', preg_replace($notAllowedCharsRegex, ' ', $name));

                    break;

                default:
                    if ($useOney) {
                        $address = $order->getShippingAddress()->getStreetLine(1);
                        $address .= $order->getShippingAddress()->getStreetLine(2) ? ' ' . $order->getShippingAddress()->getStreetLine(2) : '';

                        $micuentawebRequest->set('ship_to_street', $address);
                        $micuentawebRequest->set('ship_to_street2', null); // Not sent to FacilyPay Oney.
                    }

                    $micuentawebRequest->set('ship_to_delivery_company_name', preg_replace($notAllowedCharsRegex, ' ', $carrierName));

                    break;
            }

            $micuentawebRequest->set('ship_to_type', empty($shippingMethod['type']) ? null : $shippingMethod['type']);
            $micuentawebRequest->set('ship_to_speed', empty($shippingMethod['speed']) ? null : $shippingMethod['speed']);

            if ($shippingMethod['speed'] === 'PRIORITY') {
                $micuentawebRequest->set('ship_to_delay', empty($shippingMethod['delay']) ? null : $shippingMethod['delay']);
            }

            if ($useOney) { // Modify address to be sent to Oney.
                $micuentawebRequest->set('cust_country', 'FR'); // Send FR even address is in DOM-TOM unless form is rejected.

                $micuentawebRequest->set('ship_to_state', null); // Not sent to Oney.
                $micuentawebRequest->set('ship_to_country', 'FR'); // Send FR even address is in DOM-TOM unless form is rejected.
            }
        }
    }

    public function checkAddressValidity($address)
    {
        if (! $address) {
            return;
        }

        // Oney validation regular expressions.
        $nameRegex = "#^[A-ZÁÀÂÄÉÈÊËÍÌÎÏÓÒÔÖÚÙÛÜÇ/ '-]{1,63}$#ui";
        $phoneRegex = "#^[0-9]{10}$#";
        $cityRegex = "#^[A-Z0-9ÁÀÂÄÉÈÊËÍÌÎÏÓÒÔÖÚÙÛÜÇ/ '-]{1,127}$#ui";
        $streetRegex = "#^[A-Z0-9ÁÀÂÄÉÈÊËÍÌÎÏÓÒÔÖÚÙÛÜÇ/ '.,-]{1,127}$#ui";
        $countryRegex = "#^FR|GP|MQ|GF|RE|YT$#i";
        $zipRegex = "#^[0-9]{5}$#";

        // Address type.
        $addressType = ($address->getAddressType() === 'billing') ? 'billing address' : 'delivery address';

        $this->checkFieldValidity($address->getLastname(), $nameRegex, 'Last Name', $addressType);
        $this->checkFieldValidity($address->getFirstname(), $nameRegex, 'First Name', $addressType);
        $this->checkFieldValidity($address->getTelephone(), $phoneRegex, 'Telephone', $addressType, false);
        $this->checkFieldValidity($address->getStreetLine(1), $streetRegex, 'Address', $addressType);
        $this->checkFieldValidity($address->getStreetLine(2), $streetRegex, 'Address', $addressType, false);
        $this->checkFieldValidity($address->getPostcode(), $zipRegex, 'Postcode', $addressType);
        $this->checkFieldValidity($address->getCity(), $cityRegex, 'City', $addressType);
        $this->checkFieldValidity($address->getCountryId(), $countryRegex, 'Country', $addressType);
    }

    private function checkFieldValidity($field, $regex, $fieldName, $addressType, $mandatory = true)
    {
        // Error messages.
        $invalidMsg = 'The field %1 of your %2 is invalid.';
        $emptyMsg = 'The field %1 of your %2 is mandatory.';

        if ($mandatory && ! $field) {
            $this->setErrorMsg($emptyMsg, $fieldName, $addressType);
        }

        if ($field && ! preg_match($regex, $field)) {
            // Delete valid characters to retrieve invalid ones.
            $replaceRegex = preg_replace("/{(.*?)}/", '', $regex);
            $replaceRegex = str_replace(array('^', '$'), '', $replaceRegex);
            $invalidChars = preg_replace($replaceRegex, '', $field);

            $arr = str_split($invalidChars);
            $invalidChars = '';
            foreach ($arr as $char) {
                $invalidChars .= '<b>' . $char . '</b> ';
            }

            $this->setErrorMsg($invalidMsg, $fieldName, $addressType, $invalidChars);
        }
    }

    private function setErrorMsg($msg, $fieldName, $addressType, $invalidChars = null)
    {
        // Translate.
        $fieldName = __($fieldName);
        $addressType = __($addressType);

        $translated = __($msg, $fieldName, $addressType)->render();

        if ($invalidChars) {
            $translated .= '<br>' . __('The following characters: %1 are not allowed for this field.', $invalidChars)->render();
        }

        // Store error message in a cookie.
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setPath('/');

        $this->cookieManager->setPublicCookie(
            'micuentaweb_oney_error',
            $translated,
            $metadata
        );
    }

    public function clearErrorMsg()
    {
        if ($this->cookieManager->getCookie('micuentaweb_oney_error')) {
            $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                ->setPath('/');

            $this->cookieManager->setPublicCookie(
                'micuentaweb_oney_error',
                null,
                $metadata
            );
        }
    }
}
