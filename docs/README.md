# Byte8 FreeAgent Accounting — Documentation Site

Docusaurus 3 site for [`byte8/magento-freeagent-accounting`](../README.md).

Hosted at **https://docs.byte8.io/freeagent/** — served under the unified Byte8 docs domain via Cloudflare Pages and a path-based Worker router (see `apps/docs-router/` in the byte8.io monorepo).

## Local development

```bash
cd docs
nvm use            # picks up Node 22 from .nvmrc
pnpm install
pnpm start
```

Opens at `http://localhost:3000/freeagent/` (the `baseUrl` prefix is honoured in dev too).

## Production build

```bash
pnpm build
```

Output goes to `build/`. Deployed via **Cloudflare Pages**:

- **Project:** `docs-freeagent`
- **Build command:** `pnpm install --frozen-lockfile && pnpm build`
- **Build output:** `build`
- **Root directory:** `docs` (since this Docusaurus project sits in a subfolder of the module repo)
- **Production URL:** `https://docs.byte8.io/freeagent/`
- **Preview URL:** `https://docs-freeagent.pages.dev/freeagent/` (note the `/freeagent/` — bare root 404s because `baseUrl` is `/freeagent/`)

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
