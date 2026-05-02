<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\FreeAgentAccounting\Controller\Adminhtml\Sync;

use Byte8\Client\Api\ClientConfigInterface;
use Byte8\FreeAgentAccounting\Api\FreeAgentConfigInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;

/**
 * Renders the Byte8 → FreeAgent Sync admin page — a full-page iframe to
 * the ledger embed UI. Mirrors the Sage analogue.
 */
class Index extends Action
{
    public const ADMIN_RESOURCE = 'Byte8_FreeAgentAccounting::sync';

    public function __construct(
        Context $context,
        private readonly FreeAgentConfigInterface $freeagentConfig,
        private readonly ClientConfigInterface $clientConfig
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        if (!$this->freeagentConfig->isConnected()) {
            /** @var \Magento\Framework\Controller\Result\Redirect $redirect */
            $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $this->messageManager->addNoticeMessage(
                __('Connect to FreeAgent before opening FreeAgent Sync — there is nothing to show yet.')
            );
            return $redirect->setPath(
                'adminhtml/system_config/edit',
                ['section' => 'byte8_freeagent_accounting']
            );
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Byte8_FreeAgentAccounting::sync');
        $resultPage->getConfig()->getTitle()->prepend(__('FreeAgent Sync'));

        /** @var HttpResponse $response */
        $response = $this->getResponse();
        $response->setHeader(
            'Content-Security-Policy',
            'frame-src ' . $this->clientConfig->getBaseUrl() . ';',
            true
        );

        return $resultPage;
    }
}
