import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

const secretVault = {
    namespaced: true,
    state() {
        return {
            authToken: '',
            vaultToken: '',
            validationStatus: 'not_logged_in',
            availableVaults: [],
            vaultError: '',
            activeVault: '',
            availableVaultItems: [],
            vaultItemError: '',
            vaultItemDetails: '',
            itemDetailError: ''
        };
    },
    mutations: {
        set(state, {name, val}) {
            console.log(`Setting ${name} to `, [val])
            state[name] = val;
        }
    },
    getters: {
        authToken({ authToken }) { return authToken },
        vaultToken({ vaultToken }) { return vaultToken },
        validationStatus({ validationStatus }) { return validationStatus },
        availableVaults({ availableVaults }) { return availableVaults },
        vaultError({ vaultError }) { return vaultError },
        activeVault({ activeVault }) { return activeVault },
        availableVaultItems({ availableVaultItems }) { return availableVaultItems },
        vaultItemDetails({ vaultItemDetails }) { return vaultItemDetails },
        vaultItemError({ vaultItemError }) { return vaultItemError },
        itemDetailError({ itemDetailError }) { return itemDetailError },
        vaultId({ activeVault, availableVaults }) {
            return availableVaults[activeVault].id;
        }
    },
    actions: {
        validatePassword({ getters, commit }, password) {
            let payload = {password: password}
            console.log(getters.authToken);
            axios.post('/api/vault/entry', payload, {
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    Authorization: `Bearer ${getters.authToken}`
                }
            }).then(({ data }) => {
                commit('set', {name: 'validationStatus', val: 'success'});
            })
            .catch(({response}) => {
                let data = response.data;
                switch(data.code) {
                    case 'session_expired':
                        commit('set', {name: 'validationStatus', val: data.code});
                }
            })

        },
        fetchVaultList({ getters,commit }) {
            let payload = {token: getters.vaultToken}
            axios.post('/vault-api/vaults', payload, {
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    Authorization: `Bearer ${getters.authToken}`
                }
            }).then(({ data }) => {
                commit('set', {name: 'availableVaults', val: data.vaults});
                commit('set', {name: 'vaultError', val: ''});
            })
            .catch(({response}) => {
                    let data = response.data;
                    commit('set', {name: 'availableVaults', val: []});
                    commit('set', {name: 'vaultError', val: data.reason});
            })
        },
        fetchVaultItems({ getters,commit }, idx) {
            commit('set', {name: 'activeVault', val: idx});
            commit('set', {name: 'vaultItemError', val: ''});

            let vault = getters.availableVaults[idx];
            //let vault = getters.availableVaults[getters.activeVault];

            let payload = {
                token: getters.vaultToken,
                vaultUuid: vault['id']
            }
            axios.post('/vault-api/vaults/items', payload, {
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    Authorization: `Bearer ${getters.authToken}`
                }
            }).then(({ data }) => {
                commit('set', {name: 'availableVaultItems', val: data.items});
                commit('set', {name: 'vaultItemError', val: ''});
            })
            .catch(({response}) => {
                let data = response.data;
                commit('set', {name: 'activeVault', val: ''});
                commit('set', {name: 'availableVaultItems', val: []});
                commit('set', {name: 'vaultItemError', val: data.reason});
            })
        },
        fetchItemDetails({ getters,commit }, { vaultId, itemId }) {
            commit('set', {name: 'vaultItemDetails', val: ''});

            let payload = {
                token: getters.vaultToken,
                vaultUuid: vaultId,
                itemUuid: itemId
            }
            console.log('fetchItemDetails payload - ', payload);

            axios.post('/vault-api/vaults/items/details', payload, {
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    Authorization: `Bearer ${getters.authToken}`
                }
            }).then(({ data }) => {
                commit('set', {name: 'vaultItemDetails', val: data.details});
                commit('set', {name: 'itemDetailError', val: ''});
            })
            .catch(({response}) => {
                let data = response.data;
                commit('set', {name: 'vaultItemDetails', val: ''});
                commit('set', {name: 'itemDetailError', val: data.reason});
            })
        },
        processLockout({ getters, commit }) {

            axios.post('/access/vault/lockout', {}, {
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                }
            }).then(({ data }) => {
                window.location.href = '/access/vault'
            })
        }
    }
};

export default secretVault;
