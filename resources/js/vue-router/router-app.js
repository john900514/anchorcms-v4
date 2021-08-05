require('../bootstrap');
const Vue = require('vue');
window.Vue = Vue;
import VueRouter from "vue-router";
import Vuex from 'vuex';
Vue.use(Vuex);
Vue.use(VueRouter);

import SweetModal from 'sweet-modal-vue/src/plugin.js'
Vue.use(SweetModal)

import VuexStore from '../vuex-store/store';
window.store = VuexStore;

import router from "./";
import { mapActions } from 'vuex';
Vue.component('post-request-button', require('../vue-components/components/PostRequestButton').default);
Vue.component('secret-vault', require('../vue-components/containers/SecretVaultContainer').default);

new Vue({
    el: '#app',
    router,
    store,
    watch: {
    },
    data() {
        return {
        }
    },
    computed: {},
    methods: {
    },
    mounted() {
    },

});
