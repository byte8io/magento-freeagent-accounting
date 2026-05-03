import type { SidebarsConfig } from '@docusaurus/plugin-content-docs';

const sidebars: SidebarsConfig = {
  docsSidebar: [
    'intro',
    {
      type: 'category',
      label: 'Getting started',
      collapsed: false,
      items: [
        'getting-started/quick-start',
        'getting-started/installation',
        'getting-started/first-sync',
      ],
    },
    {
      type: 'category',
      label: 'Connect',
      items: [
        'connect/pairing-code',
        'connect/freeagent-oauth',
        'connect/disconnect',
      ],
    },
    {
      type: 'category',
      label: 'Sync settings',
      items: [
        'settings/sync-behavior',
        'settings/default-mappings',
        'settings/payment-methods',
        'settings/item-type-map',
        'settings/commercial',
      ],
    },
    {
      type: 'category',
      label: 'Magento admin',
      items: [
        'magento-admin/freeagent-status-grid',
        'magento-admin/freeagent-status-detail',
        'magento-admin/dead-letter-banner',
      ],
    },
    'what-syncs',
    'troubleshooting',
    'faq',
  ],
};

export default sidebars;
