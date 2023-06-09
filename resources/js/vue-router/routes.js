const routes = [
    {
        path: '/access/vault/entry',
        component: () => import(/* webpackChunkName: "vault-index" */ '../vue-pages/secrets-vault/VaultIndex.vue'),
        name: 'vault'
    },
    {
        path: '/access/vault/vaults',
        component: () => import(/* webpackChunkName: "list-of-vaults-page" */'../vue-pages/secrets-vault/ListOfVaultsPage.vue'),
        name: 'vault-list'
    },
    {
        path: '/access/vault/items',
        component: () => import(/* webpackChunkName: "vault-items-page" */'../vue-pages/secrets-vault/VaultItemsPage.vue'),
        name: 'vault-items'
    }
];

export default routes;
