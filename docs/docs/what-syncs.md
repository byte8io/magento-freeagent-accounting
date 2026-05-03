---
title: What syncs
description: Entity-by-entity matrix — which Magento events land in which FreeAgent entities.
---

# What syncs

The full Magento → FreeAgent entity matrix. Direction is **always** `M → F` in v1 (Magento source of truth, FreeAgent ledger of record).

For per-plan feature gating (which entities are available on which tier), see the **[Plans & pricing page on byte8.io](https://byte8.io/products/freeagent-accounting#pricing)**.

## Entities

| Magento event | FreeAgent entity | What's posted |
|---|---|---|
| **`invoice.created`** | `invoice` (status: per [Commercial knobs](/docs/settings/commercial)) | Invoice with full line items, addresses, currency, per-line discounts, dedicated shipping line. Always carries `payment_terms_in_days` (FreeAgent quirk #1). |
| **`invoice.paid`** | `bank_transaction_explanation` | Auto-payment routed per [Payment-method map](/docs/settings/payment-methods); attaches against the matching FreeAgent invoice URL via `entity_xref`. The invoice transitions Open → Paid in FreeAgent. |
| **`creditmemo.created`** | `credit_note` | Refund routed to the same contact as the original invoice. Includes shipping refund line + original-invoice date for accountant linkage. |
| **`customer.upserted`** | `contact` | Magento customer → FreeAgent contact. Lookup-by-email on duplicate POST 422 means a customer that already exists in FreeAgent is reused (no duplicate). |
| **`product.upserted`** (`sync_products = true`) | `product` (`item_type` = Products / Services per [Item-type map](/docs/settings/item-type-map)) | Magento simple → FreeAgent `Products`; virtual / downloadable → `Services` by default; configurable. |

## What's NOT synced (intentionally, in v1)

- **Standalone payments without an invoice.** Magento has no API to attach an offline payment to an existing invoice; FreeAgent's `bank_transaction_explanation` requires an invoice URL to attach against. The chassis intentionally doesn't ship a `payment.captured` flow — accountants reconcile offline payments manually in FreeAgent.
- **FreeAgent → Magento writeback.** Enterprise on request — needs FreeAgent webhook surface + Magento write endpoints + conflict-resolution policy.
- **Inventory writes from FreeAgent.** FreeAgent's product catalog isn't designed to track stock the way Sage's `stock_item` family is. The chassis doesn't sync stock movements to FreeAgent.
- **Composite product types** (`configurable`, `bundle`, `grouped`). Skipped at translate time with `reason: PRODUCT_TYPE_NOT_SUPPORTED` — FreeAgent doesn't model variants the way Magento does. Their child simples sync individually.
- **Tier pricing + special pricing.** Only `price` is transmitted on the catalog upsert. Future config knobs (`price_strategy: base | special | lowest`, `tier_pricing_enabled`) are deferred to a real merchant ask.
- **Estimates.** Estimates supported on higher tiers — see the [Plans & pricing page](https://byte8.io/products/freeagent-accounting#pricing) for tier gating.
- **Projects + timeslips.** FreeAgent's project-based time-tracking surface is out-of-scope for an e-commerce connector. Pure manual flow on FreeAgent's side.

## Idempotency keys

Every event carries a stable idempotency key:

| Event | Key shape |
|---|---|
| `invoice.created` | `invoice.created:{entity_id}` |
| `invoice.paid` | `invoice.paid:{entity_id}` |
| `creditmemo.created` | `creditmemo.created:{entity_id}` |
| `customer.upserted` | `customer.upserted:{entity_id}` |
| `product.upserted` | `product.upserted:{entity_id}` |

The chassis dedupes on these keys so observer re-fires, duplicate Magento saves, and replays are safe — never produces duplicate FreeAgent entities.

The chassis also dedupes downstream via `entity_xref` (Magento entity_id ↔ FreeAgent entity URL). This is the second line of defence: if a chassis-side bug ever caused a duplicate POST, the `entity_xref` lookup catches it and routes to the existing FreeAgent entity. For contacts specifically, the chassis additionally handles FreeAgent's "duplicate email" 422 by re-fetching the existing contact by email before failing the run.

## Sync filters in priority order

What gets to FreeAgent is gated by the binding's sync policy:

1. **`sync_unpaid_invoices: false`** filters out unpaid invoices entirely.
2. **`sync_zero_value_invoices: false`** filters out £0 invoices.
3. **`sync_since`** filters out everything before the cutover date.
4. **`website_filter`** + **`store_filter`** restrict to specific Magento sites.
5. **`sync_products: false`** (default) filters out the entire `product.upserted` event stream.

Skips are auditable in the dashboard sync history with stable reason codes.

## Plan-gated features

Some entities (credit notes, payments, products, multi-store) require higher-tier plans. The full per-plan feature matrix lives on the **[Plans & pricing page](https://byte8.io/products/freeagent-accounting#pricing)**.

If you try to enable a feature your plan doesn't include (e.g. flipping on `sync_products` outside its tier), the chassis blocks it server-side with a clear `tier_limit_exceeded` validation error on the policy save.
