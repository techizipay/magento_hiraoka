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
 * Custom renderer for the submodule logo.
 */
class Logo extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Lyranetwork\Micuentaweb\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Lyranetwork\Micuentaweb\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Lyranetwork\Micuentaweb\Helper\Data $dataHelper,
        $data = []
    ) {
        $this->dataHelper = $dataHelper;

        parent::__construct($context, $data);
    }

    /**
     * Set default image URL.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = parent::render($element);

        $fileName = $element->getValue();
        if ($fileName && ! $this->dataHelper->isUploadFileImageExists($fileName)) {
            // Default logo defined in the module.
            $imgSrc = $this->getViewFileUrl('Lyranetwork_Micuentaweb::images/' . $fileName);

            $html = preg_replace('#src="https?://[^"]+"#', 'src="' . $imgSrc . '"', $html);
        }

        return $html;
    }
}
