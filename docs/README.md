# Byte8 FreeAgent Accounting — Documentation Site

Docusaurus 3 site for [`byte8/magento-freeagent-accounting`](../README.md).

Hosted at **https://magento-freeagent.byte8.dev**.

## Local development

```bash
cd docs
nvm use            # picks up Node 20+
pnpm install
pnpm start
```

Opens at `http://localhost:3000/`.

## Production build

```bash
pnpm build
```

Output goes to `build/`. Deploy that directory to any static host (the
repo's GitHub Pages workflow handles `magento-freeagent.byte8.dev`).

## Editing

- **Doc pages** live under `docs/docs/` — mirror the sidebar order in
  `sidebars.ts`.
- **Homepage** (`/`) lives under `src/pages/index.tsx`. There's no
  `/pricing` page in the docs site — the navbar + footer Pricing
  links point straight to `byte8.io/products/freeagent-accounting` so
  the marketing site stays the single source of truth for commercial
  details.
- **Theme overrides** live in `src/css/custom.css` — FreeAgent blue
  accent (`#5B8DEF`), matching the byte8.io marketing aesthetic and
  the `/products/freeagent-accounting` page exactly. Don't edit
  Docusaurus defaults inline; change the variables in the `:root` and
  `[data-theme='dark']` blocks.
- **Blog** = changelog. One markdown file per release under `blog/`,
  authored as `byte8` (see `blog/authors.yml`).
