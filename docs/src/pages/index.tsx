import Link from '@docusaurus/Link';
import Layout from '@theme/Layout';
import styles from './index.module.css';

interface FeatureCardProps {
  href: string;
  icon: string;
  title: string;
  body: string;
  cta: string;
}

function FeatureCard({ href, icon, title, body, cta }: FeatureCardProps) {
  return (
    <Link to={href} className={styles.featureCard}>
      <span className={styles.featureIcon} aria-hidden>{icon}</span>
      <h3 className={styles.featureTitle}>{title}</h3>
      <p className={styles.featureBody}>{body}</p>
      <span className={styles.featureFooter}>{cta} ↘</span>
    </Link>
  );
}

export default function Home(): React.ReactElement {
  return (
    <Layout
      title="Byte8 FreeAgent Accounting — Magento 2 to FreeAgent"
      description="Hosted SaaS connector between Magento 2 and FreeAgent. Invoices, credit notes, customers, products, payments. Audited. Hands-off."
    >
      <main>
        {/* Hero */}
        <section className={styles.heroSection}>
          <div className={styles.heroContent}>
            <span className={styles.eyebrow}>Magento 2 · FreeAgent · Hosted SaaS</span>
            <h1 className={styles.heroTitle}>
              Magento 2 → FreeAgent.{' '}
              <span className={styles.heroTitleAccent}>Hands-off.</span>
            </h1>
            <p className={styles.heroSubtitle}>
              Invoices, credit notes, customers, products, payments — synced
              from Magento into FreeAgent within minutes. Bank-transaction
              explanations attached against the matching invoice automatically.
              Full audit trail per sync run. We host the connector — you install
              a thin Magento module and forget about it.
            </p>
            <div className={styles.heroCtas}>
              <Link className="button button--primary button--lg" to="/docs/getting-started/quick-start">
                Quick start
              </Link>
              <Link className="button button--secondary button--lg" to="/docs/">
                Read the docs
              </Link>
            </div>

            <div className={styles.statsRow}>
              <div className={styles.stat}>
                <span className={styles.statValue}>&lt; 60s</span>
                <span className={styles.statLabel}>Cron drain interval</span>
              </div>
              <div className={styles.stat}>
                <span className={styles.statValue}>5</span>
                <span className={styles.statLabel}>Magento entities synced</span>
              </div>
              <div className={styles.stat}>
                <span className={styles.statValue}>0 OAuth</span>
                <span className={styles.statLabel}>You handle in Magento</span>
              </div>
              <div className={styles.stat}>
                <span className={styles.statValue}>SaaS</span>
                <span className={styles.statLabel}>Centrally patched</span>
              </div>
            </div>
          </div>
        </section>

        {/* Core capabilities */}
        <section className={styles.section}>
          <header className={styles.sectionHeader}>
            <span className={styles.sectionEyebrow}>Core sync</span>
            <p className={styles.sectionLead}>
              Five Magento entity events flow into FreeAgent automatically —
              with full idempotency, retry, and audit on every step.
            </p>
          </header>

          <div className={styles.cardGrid}>
            <FeatureCard
              href="/docs/what-syncs"
              icon="🧾"
              title="Invoices + payments"
              body="Magento invoices land in FreeAgent as Open AR the moment they're raised. invoice.paid attaches a bank_transaction_explanation against the matching FreeAgent invoice automatically. B2B net-terms flows leave invoices Open for manual reconciliation."
              cta="What syncs"
            />
            <FeatureCard
              href="/docs/what-syncs"
              icon="↩️"
              title="Credit notes"
              body="Magento credit memos sync as FreeAgent credit notes with original-invoice linkage. Offline-payment refunds (no parent invoice) handled. Same per-line discount + per-line tax invariants as invoices."
              cta="Credit memos"
            />
            <FeatureCard
              href="/docs/settings/default-mappings"
              icon="📚"
              title="Income categories"
              body="Set a default FreeAgent income category URL on the binding — every invoice line that doesn't carry its own routing books to it. Live-validated against your account's category list, so typos surface form-time, not as a 422 dead-letter."
              cta="Default mappings"
            />
          </div>
        </section>

        {/* Visibility */}
        <section className={styles.section}>
          <header className={styles.sectionHeader}>
            <span className={styles.sectionEyebrow}>Magento admin visibility</span>
            <p className={styles.sectionLead}>
              See sync status without leaving the Magento admin you already
              live in.
            </p>
          </header>

          <div className={styles.cardGrid}>
            <FeatureCard
              href="/docs/magento-admin/freeagent-status-grid"
              icon="🔵"
              title="FreeAgent Status chip on grids"
              body="Sortable + filterable FreeAgent Status column on Sales → Invoices and Sales → Credit Memos. Pending / Synced / Skipped / Failed chips with hover tooltips for the underlying FreeAgent reference, skip-reason, or error code."
              cta="Status grid"
            />
            <FeatureCard
              href="/docs/magento-admin/freeagent-status-detail"
              icon="📒"
              title="Detail-page info block"
              body="Every Invoice and Credit Memo detail page gets a FreeAgent info block beside Order Information — chip, FreeAgent entity reference, last sync timestamp, skip / error context."
              cta="Detail block"
            />
            <FeatureCard
              href="/docs/magento-admin/dead-letter-banner"
              icon="🚨"
              title="Dead-letter banner"
              body="Failed deliveries surface as a banner on the admin config page — operator-visible without log diving. Per-row retry from the ledger dashboard re-enters the queue cleanly."
              cta="Dead-letter handling"
            />
          </div>
        </section>

        {/* SaaS chassis */}
        <section className={styles.section}>
          <header className={styles.sectionHeader}>
            <span className={styles.sectionEyebrow}>SaaS chassis</span>
            <p className={styles.sectionLead}>
              The Magento module is thin by design. The heavy lifting lives in
              the hosted ledger — so you never patch your connector.
            </p>
          </header>

          <div className={styles.cardGrid}>
            <FeatureCard
              href="/docs/connect/freeagent-oauth"
              icon="🔐"
              title="No OAuth in PHP"
              body="FreeAgent OAuth lives entirely in our hosted ledger SaaS. Magento never talks to api.freeagent.com directly — no client secret on disk, no token rotation logic on your server, no breaking-API patches to ship."
              cta="OAuth flow"
            />
            <FeatureCard
              href="/docs/connect/pairing-code"
              icon="🤝"
              title="Pairing-code Connect"
              body="Generate a 30-min pairing code in your Magento admin, paste it into ledger.byte8.io, and the chassis handshakes back in. No callback URL wrangling, no app secret on disk."
              cta="Connect flow"
            />
            <FeatureCard
              href="/docs/troubleshooting"
              icon="🔁"
              title="Centrally-patched"
              body="FreeAgent API breaks? We patch the chassis and every connected merchant gets the fix. The two known FreeAgent v2 quirks (payment_terms_in_days required on every POST, stringified numbers on responses) already catalogued and worked around — your invoice flow stays green through API drift."
              cta="Troubleshooting"
            />
          </div>
        </section>

        {/* CTA band */}
        <section className={styles.ctaBand}>
          <h2 className={styles.ctaTitle}>60 seconds to live sync.</h2>
          <p className={styles.ctaSubtitle}>
            <code>composer require byte8/magento-freeagent-accounting</code> · run setup:upgrade · pair with ledger.byte8.io.
          </p>
          <div className={styles.heroCtas}>
            <Link className="button button--primary button--lg" to="/docs/getting-started/quick-start">
              Quick start
            </Link>
            <Link className="button button--secondary button--lg" to="https://byte8.io/products/freeagent-accounting">
              Plans & pricing
            </Link>
          </div>
        </section>
      </main>
    </Layout>
  );
}
