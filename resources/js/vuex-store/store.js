import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);
import usersCrud from "./modules/usersCrud";
import secretVault from "./modules/secretVault";

export default new Vuex.Store({
    modules: {
        usersCrud,
        secretVault
    },
    state() {
        return {

        };
    },
    mutations: {

    },
    getters: {

    },
    actions: {

    }
});
