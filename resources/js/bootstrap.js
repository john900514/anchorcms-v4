window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */
try {
    //window.$ = window.jQuery = require('jquery');
    //window.Noty = require('noty');
    //require('bootstrap');
    window.select2 = require ('select2');
    window.Popper = require('popper.js').default;
    /*


    require('bootstrap-iconpicker/bootstrap-iconpicker/js/bootstrap-iconpicker');
    require('jquery-loadingModal/js/jquery.loadingModal.min');


 */
} catch (e) {}

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

window.Vapor = require('laravel-vapor');
// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

