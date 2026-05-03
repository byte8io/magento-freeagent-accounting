---
sidebar_position: 4
title: Item-type map
description: Magento product type → FreeAgent item type. Decides whether each product upsert lands as a FreeAgent product, service, or other type.
---

# Item-type map

`ledger.byte8.io/dashboard/bindings/{id}/settings` → **FreeAgent Item-type Map** card. (Visible only on bindings with `sync_products = true`.)

FreeAgent's product catalog distinguishes a few item kinds — broadly `Products` (physical goods, often inventoried) and `Services` (labour, subscriptions, digital deliverables). Magento expresses this differently — simple products with stock vs virtual / downloadable products with no stock. The map translates between the two systems.

## The default routing

If the map is empty, the chassis applies these defaults:

| Magento product type | FreeAgent `item_type` |
|---|---|
| `simple` | `Products` |
| `virtual` | `Services` |
| `downloadable` | `Services` |
| `configurable` / `bundle` / `grouped` | _skipped_ (composite types not modelled in FreeAgent) |

For most merchants, the defaults match how their accountant already books these in FreeAgent. The map exists for the cases where they don't.

## When to override

- **You sell digital goods that you want booked as Products, not Services.** Some merchants prefer the inventory-style display in FreeAgent's reports even for downloadable assets. Map `downloadable → Products`.
- **You sell physical goods that are essentially services** (e.g. installation kits where the physical item is a token and the value is the install). Map `simple → Services`.
- **Your accountant has set up a custom FreeAgent item type** through their Settings → Categories surface and wants Magento simples to land there. Map per Magento type accordingly — the dropdown lists every item type from your FreeAgent reference cache.

## What lands where

The chassis's `product.upserted` translator decides at write-time:

```rust
let item_type = item_type_map
    .get(magento_product_type)
    .copied()
    .unwrap_or_else(|| default_for_type(magento_product_type));

POST /v2/products
{
  "product": {
    "name":        "...",
    "description": "...",
    "price":       "...",
    "sales_price": "...",
    "item_type":   item_type,   // "Products" or "Services"
    ...
  }
}
```

Subsequent updates to the same Magento SKU re-submit (PUT) against the same FreeAgent product URL stored in `entity_xref`, so changing the map after an initial sync only affects newly-created products — existing FreeAgent products keep their original `item_type` (FreeAgent doesn't accept item-type changes on updates).

## Migrating an existing catalog

If you flipped `sync_products = true` with the default map, then later realised you'd rather have all your downloadable products booked as `Products` (not `Services`), and you've already synced N products with the wrong type:

1. Update the map (downloadable → Products).
2. Email `helo@byte8.io` — we have a chassis CLI (`ledger-cli xref:reset --provider freeagent --kind product`) that nukes the relevant `entity_xref` rows so the next product upsert re-creates the FreeAgent product with the new `item_type`. (Manual run only — there's footgun risk on multi-tenant chassis if invoked broadly.)

There's no UI for this yet because the only design partner who's hit it had a 12-product catalog; we did it manually. v1.1+ if a larger-catalog merchant asks.

## Composite product types

`configurable`, `bundle`, `grouped` products **never sync to FreeAgent**. Skipped at translate time with `reason: PRODUCT_TYPE_NOT_SUPPORTED`. Their child simples sync individually via the same observer when those simples are saved.

This is intentional — FreeAgent doesn't model variants the way Magento does, and folding configurables down to one row in FreeAgent loses information without giving you anything back. The accountant works off the line items on the invoice (which carry the child simple's name + price), not the catalog rollup.

## Validation

Map keys must be one of: `simple`, `virtual`, `downloadable`. Map values must be one of the FreeAgent item types from your reference cache (today: `Products`, `Services`). Anything else surfaces a `not_in_reference` field error before save.
