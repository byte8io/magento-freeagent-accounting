<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\FreeAgentAccounting\Model\ResourceModel\Sales;

use Byte8\Client\Api\Data\EntitySyncStateInterface;
use Byte8\FreeAgentAccounting\Api\FreeAgentConfigInterface;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\Grid\Collection as MagentoCreditmemoGridCollection;

/**
 * Credit Memo grid collection with the byte8_entity_sync_state JOIN
 * baked in via `_initSelect()`. Mirror of the Sage analogue —
 * filtered to `provider = 'freeagent'`.
 */
class CreditmemoGridCollection extends MagentoCreditmemoGridCollection
{
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addSyncStateJoin();
        return $this;
    }

    private function addSyncStateJoin(): void
    {
        if ($this->getFlag('byte8_sync_state_joined')) {
            return;
        }
        $this->setFlag('byte8_sync_state_joined', true);

        $select = $this->getSelect();
        $connection = $this->getConnection();

        // Aliases suffixed `_freeagent` to coexist with Sage's identical
        // JOIN — see InvoiceGridCollection comment for the full rationale.
        $select->joinLeft(
            ['byte8_sync_fa' => $this->getTable(EntitySyncStateInterface::DB_TABLE_NAME)],
            sprintf(
                "byte8_sync_fa.entity_type = %s AND byte8_sync_fa.magento_id = main_table.entity_id AND byte8_sync_fa.provider = %s",
                $connection->quote(EntitySyncStateInterface::ENTITY_TYPE_CREDITMEMO),
                $connection->quote(FreeAgentConfigInterface::PROVIDER_KEY)
            ),
            [
                'byte8_sync_status_freeagent' => 'byte8_sync_fa.sync_status',
                'byte8_sync_provider_entity_id_freeagent' => 'byte8_sync_fa.provider_entity_id',
                'byte8_sync_provider_reference_freeagent' => 'byte8_sync_fa.provider_reference',
                'byte8_sync_skip_reason_freeagent' => 'byte8_sync_fa.skip_reason',
                'byte8_sync_error_code_freeagent' => 'byte8_sync_fa.error_code',
                'byte8_sync_last_sync_at_freeagent' => 'byte8_sync_fa.last_sync_at',
            ]
        );
    }
}
