<?php
/**
 * Copyright © Lyra Network.
 * This file is part of Mi Cuenta Web plugin for Magento 2. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Lyranetwork\Micuentaweb\Controller\Payment\Rest;

use Lyranetwork\Micuentaweb\Model\ResponseException;
use Magento\Framework\DataObject;

class Response extends \Lyranetwork\Micuentaweb\Controller\Payment\Response
{
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Lyranetwork\Micuentaweb\Model\Api\MicuentawebResponseFactory
     */
    protected $micuentawebResponseFactory;

    /**
     * @var \Lyranetwork\Micuentaweb\Helper\Rest
     */
    protected $restHelper;

    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    protected $quoteManagement;

    /**
     * @var \Magento\Checkout\Model\Type\Onepage
     */
    protected $onepage;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Lyranetwork\Micuentaweb\Controller\Processor\ResponseProcessor $responseProcessor
     * @param \Lyranetwork\Micuentaweb\Controller\Result\RedirectFactory $micuentawebRedirectFactory
     * @param \Lyranetwork\Micuentaweb\Helper\Rest $restHelper
     * @param \Magento\Quote\Api\CartManagementInterface $quoteManagement
     * @param \Magento\Checkout\Model\Type\Onepage $onepage
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Lyranetwork\Micuentaweb\Controller\Processor\ResponseProcessor $responseProcessor,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lyranetwork\Micuentaweb\Helper\Rest $restHelper,
        \Magento\Quote\Api\CartManagementInterface $quoteManagement,
        \Magento\Checkout\Model\Type\Onepage $onepage
    ) {
        $this->restHelper = $restHelper;
        $this->quoteManagement = $quoteManagement;
        $this->onepage = $onepage;
        $this->orderFactory = $responseProcessor->getOrderFactory();
        $this->micuentawebResponseFactory = $responseProcessor->getMicuentawebResponseFactory();

        parent::__construct($context, $quoteRepository, $responseProcessor, $resultPageFactory);
    }

    public function execute()
    {
        // Clear quote data.
        $this->dataHelper->getCheckout()->unsLastQuoteId()
            ->unsLastSuccessQuoteId()
            ->clearHelperData();

        return parent::execute();
    }

    protected function prepareResponse($params)
    {
        // Check the validity of the request.
        if (! $this->restHelper->checkResponseFormat($params)) {
            throw new ResponseException('Invalid response received. Content: ' . json_encode($params));
        }

        $answer = json_decode($params['kr-answer'], true);
        if (! is_array($answer)) {
            throw new ResponseException('Invalid response received. Content: ' . json_encode($params));
        }

        // Wrap payment result to use traditional order creation tunnel.
        $data = $this->restHelper->convertRestResult($answer);

        // Convert REST result to standard form response.
        $response = $this->micuentawebResponseFactory->create(
            [
                'params' => $data,
                'ctx_mode' => null,
                'key_test' => '',
                'key_prod' => '',
                'algo' => null
            ]
        );

        $quoteId = (int) $response->getExtInfo('quote_id');
        if (! $quoteId || ! $this->quoteRepository->get($quoteId)->getId()) {
            throw new ResponseException("Quote #{$quoteId} not found in database.");
        }

        $quote = $this->quoteRepository->get($quoteId);

        // Disable quote.
        if ($quote->getIsActive()) {
            $quote->setIsActive(false);
            $this->quoteRepository->save($quote);
            $this->dataHelper->log("Cleared quote, reserved order ID: #{$quote->getReservedOrderId()}.");
        }

        // Token is created before order creation, search order by quote.
        $order = $this->orderFactory->create();
        $order->loadByIncrementId($quote->getReservedOrderId());

        if (! $order->getId()) {
            // Reload onepage context.
            $this->saveOrderForQuote($quote);

            // Dispatch save order event.
            $result = new DataObject();
            $result->setData('success', true);
            $result->setData('error', false);

            $this->_eventManager->dispatch(
                'checkout_controller_onepage_saveOrder',
                [
                    'result' => $result,
                    'action' => $this
                ]
            );

            // Load newly created order.
            $order->loadByIncrementId($quote->getReservedOrderId());
            if (! $order->getId()) {
                throw new ResponseException("Order cannot be created for quote #{$quoteId}.");
            }

            $this->dataHelper->log("Order #{$order->getIncrementId()} has been created for quote #{$quoteId}.");
        } else {
            $this->dataHelper->log("Found order #{$order->getIncrementId()} for quote #{$quoteId}.");
        }

        $storeId = $order->getStore()->getId();

        // Check the authenticity of the request.
        if (! $this->restHelper->checkResponseHash($params, $this->restHelper->getReturnKey($storeId))) {
            // Authentication failed.
            throw new ResponseException(
                "{$this->dataHelper->getIpAddress()} tries to access micuentaweb/payment_rest/response page without valid signature with parameters: " . json_encode($params)
            );
        }

        return [
            'response' => $response,
            'order' => $order
        ];
    }

    protected function saveOrderForQuote($quote)
    {
        $this->onepage->setQuote($quote);
        $this->onepage->saveOrder();
    }
}
