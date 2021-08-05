require('./bootstrap');
const Vue = require('vue');
window.Vue = Vue;
import Vuex from 'vuex';
Vue.use(Vuex);

import SweetModal from 'sweet-modal-vue/src/plugin.js'
Vue.use(SweetModal)

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */
Vue.component('welcome', require('./vue-components/containers/WelcomeContainer').default);
Vue.component('video-bg', require('./vue-components/components/AnimatedBackgroundComponent').default);
Vue.component('launch-a-modal-button', require('./vue-components/components/LaunchASweetModalButton').default);
Vue.component('post-request-button', require('./vue-components/components/PostRequestButton').default);

Vue.component('user-roles-select', require('./vue-components/components/usersCrud/UserRolesSelectComponent').default);
Vue.component('user-client-select', require('./vue-components/components/usersCrud/UserClientSelectComponent').default);
Vue.component('location-dept-select', require('./vue-components/components/usersCrud/LocationDeptSelectComponent').default);


import VuexStore from './vuex-store/store';
window.store = VuexStore;

import { mapActions } from 'vuex';

new Vue({
    el: '#app',
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
