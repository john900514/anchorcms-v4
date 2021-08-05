import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

const usersCrud = {
    namespaced: true,
    state() {
        return {
            role: '',
            locations: '',
            client: ''
        };
    },
    mutations: {
        role(state, role) {
            console.log('Form Role set to '+role);
            state.role = role;

            switch(role) {
                case 'developer':
                    state.locations = '';
                    state.client = '';
                    break;

                case 'admin':
                    state.locations = '';
                    state.client = '';
                    break;

                case 'executive':
                    state.locations = '';
                    break;
            }

        },
        client(state, client) {
            console.log('Form client set to '+client);
            state.client = client;
        },
        locations(state, locations) {
            state.locations = locations;
        }
    },
    getters: {
        role({role}) {
            return role;
        },
        locations({locations}) {
            return locations;
        },
        client({client}) {
            return client
        }
    },
    actions: {
        getClientLocations({ commit, getters }) {
            let url = '/internal-api/locations/'+getters.client;

            axios.get(url)
                .then(({ data }) => {
                    commit('locations', data);
                    console.log(data)
                })
                .catch(err => {
                    alert('Fuck!')
                    console.log(err)
                })
        },
        getCapeAndBayDepartments({ commit }) {
            let url = '/internal-api/locations/cnb';

            axios.get(url)
                .then(({ data }) => {
                    commit('locations', data);
                    console.log(data)
                })
                .catch(err => {
                    alert('Fuck!')
                    console.log(err)
                })
        }
    }
};

export default usersCrud;
