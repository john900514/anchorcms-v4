const routes = [
    {
        path: '/access/vault/entry',
        component: () => import('../vue-pages/secrets-vault/VaultIndex'),
        name: 'vault'
    },
    {
        path: '/access/vault/vaults',
        component: () => import('../vue-pages/secrets-vault/ListOfVaultsPage'),
        name: 'vault-list'
    },
    {
        path: '/access/vault/items',
        component: () => import('../vue-pages/secrets-vault/VaultItemsPage'),
        name: 'vault-items'
    },
    {
        path: '/access/vault/item',
        component: () => import('../vue-pages/secrets-vault/VaultItemDetailPage'),
        name: 'vault-item'
    },
];

export default routes;
