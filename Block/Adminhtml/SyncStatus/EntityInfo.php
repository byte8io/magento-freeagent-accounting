<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\FreeAgentAccounting\Block\Adminhtml\SyncStatus;

use Byte8\Client\Api\Data\EntitySyncStateInterface;
use Byte8\Client\Api\EntitySyncStateRepositoryInterface;
use Byte8\FreeAgentAccounting\Api\FreeAgentConfigInterface;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;

/**
 * "FreeAgent Accounting" admin info block on Sales → Invoice /
 * Credit Memo detail pages. Mirror of the Sage analogue, scoped
 * to `provider = 'freeagent'`.
 */
class EntityInfo extends Template
{
    private const REGISTRY_KEYS = [
        EntitySyncStateInterface::ENTITY_TYPE_INVOICE    => 'current_invoice',
        EntitySyncStateInterface::ENTITY_TYPE_CREDITMEMO => 'current_creditmemo',
    ];

    public function __construct(
        Context $context,
        private readonly Registry $registry,
        private readonly EntitySyncStateRepositoryInterface $syncStateRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getEntityType(): string
    {
        return (string) $this->getData('entity_type');
    }

    public function getSyncState(): ?EntitySyncStateInterface
    {
        $entityType = $this->getEntityType();
        $registryKey = self::REGISTRY_KEYS[$entityType] ?? null;
        if ($registryKey === null) {
            return null;
        }

        $entity = $this->registry->registry($registryKey);
        if ($entity === null) {
            return null;
        }

        $magentoId = method_exists($entity, 'getEntityId')
            ? (int) $entity->getEntityId()
            : 0;
        if ($magentoId <= 0) {
            return null;
        }

        return $this->syncStateRepository->find(
            $entityType,
            $magentoId,
            FreeAgentConfigInterface::PROVIDER_KEY
        );
    }

    public function getStatusLabel(?string $syncStatus): string
    {
        return match ($syncStatus) {
            EntitySyncStateInterface::STATUS_SYNCED  => __('✓ Synced')->getText(),
            EntitySyncStateInterface::STATUS_PENDING => __('⏳ Pending')->getText(),
            EntitySyncStateInterface::STATUS_SKIPPED => __('⏸ Skipped')->getText(),
            EntitySyncStateInterface::STATUS_FAILED  => __('✗ Failed')->getText(),
            default                                   => __('—')->getText(),
        };
    }

    public function getStatusCssClass(?string $syncStatus): string
    {
        return match ($syncStatus) {
            EntitySyncStateInterface::STATUS_SYNCED  => 'byte8-sync-chip byte8-sync-chip--synced',
            EntitySyncStateInterface::STATUS_PENDING => 'byte8-sync-chip byte8-sync-chip--pending',
            EntitySyncStateInterface::STATUS_SKIPPED => 'byte8-sync-chip byte8-sync-chip--skipped',
            EntitySyncStateInterface::STATUS_FAILED  => 'byte8-sync-chip byte8-sync-chip--failed',
            default                                   => 'byte8-sync-chip byte8-sync-chip--none',
        };
    }
}
