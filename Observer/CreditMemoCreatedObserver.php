<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\FreeAgentAccounting\Observer;

use Byte8\Client\Api\ByteClientInterface;
use Byte8\FreeAgentAccounting\Api\FreeAgentConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Psr\Log\LoggerInterface;

/**
 * Publishes `creditmemo.created` on `sales_order_creditmemo_save_after`.
 * Id-only payload — ledger's worker fetches the canonical credit memo
 * via `GET /V1/byte8/creditmemo/:id` once the transaction commits and
 * dedupes repeated posts via `entity_xref`.
 */
class CreditMemoCreatedObserver implements ObserverInterface
{
    public function __construct(
        private readonly ByteClientInterface $byteClient,
        private readonly FreeAgentConfigInterface $freeagentConfig,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(Observer $observer): void
    {
        if (!$this->freeagentConfig->isConnected()) {
            return;
        }

        $memo = $observer->getEvent()->getData('creditmemo');
        if (!$memo instanceof CreditmemoInterface) {
            return;
        }

        $entityId = (int) $memo->getEntityId();
        if ($entityId <= 0) {
            return;
        }

        try {
            $this->byteClient->enqueueEvent('creditmemo.created', [
                'magento_entity_id' => $entityId,
                'website_id'        => $this->resolveWebsiteId($memo),
                'store_id'          => (int) $memo->getStoreId(),
                'occurred_at'       => gmdate('Y-m-d\TH:i:s\Z'),
                'payload'           => [
                    'increment_id' => (string) $memo->getIncrementId(),
                ],
            ], 'creditmemo.created:' . $entityId, FreeAgentConfigInterface::PROVIDER_KEY);
        } catch (\Throwable $e) {
            $this->logger->error(
                'Byte8: freeagent creditmemo.created observer failed to enqueue for memo ' . $entityId
                . ': ' . $e->getMessage()
            );
        }
    }

    private function resolveWebsiteId(CreditmemoInterface $memo): int
    {
        if (method_exists($memo, 'getOrder')) {
            $order = $memo->getOrder();
            if ($order && method_exists($order, 'getStore')) {
                $store = $order->getStore();
                if ($store && method_exists($store, 'getWebsiteId')) {
                    return (int) $store->getWebsiteId();
                }
            }
        }
        return 0;
    }
}
