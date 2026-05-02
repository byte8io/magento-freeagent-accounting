<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\FreeAgentAccounting\Block\Adminhtml\System\Config;

use Byte8\FreeAgentAccounting\Api\FreeAgentConfigInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Renders the Disconnect button. Disabled while not connected.
 */
class DisconnectButton extends Field
{
    protected $_template = 'Byte8_FreeAgentAccounting::system/config/disconnect_button.phtml';

    public function __construct(
        Context $context,
        private readonly FreeAgentConfigInterface $freeagentConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->_toHtml();
    }

    public function getDisconnectUrl(): string
    {
        return $this->getUrl('byte8freeagentaccounting/connection/disconnect');
    }

    public function isConnected(): bool
    {
        return $this->freeagentConfig->isConnected();
    }

    public function getButtonHtml(): string
    {
        /** @var Button $button */
        $button = $this->getLayout()->createBlock(Button::class)
            ->setData([
                'id' => 'byte8_freeagent_disconnect_button',
                'label' => __('Disconnect'),
                'class' => 'action-secondary',
                'disabled' => !$this->isConnected(),
            ]);
        return $button->toHtml();
    }
}
