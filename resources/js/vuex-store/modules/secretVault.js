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
            vaultError: ''
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
        }
    }
};

export default secretVault;
