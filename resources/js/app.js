/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */


require('./bootstrap');

require('./registerServiceWorker');
window.Vue = require('vue');

import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import { BootstrapVue } from 'bootstrap-vue';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faCog, faQuestionCircle, faUserEdit, faBarcode, faUserMinus } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import VueTippy, { TippyComponent } from "vue-tippy";
import Snotify from 'vue-snotify';
import VueCountdownTimer from 'vuejs-countdown-timer';

library.add(faCog);
library.add(faQuestionCircle);
library.add(faUserEdit);
library.add(faUserMinus);
library.add(faBarcode);
Vue.config.productionTip = false;

Vue.use(VueCountdownTimer);

Vue.use(Loading);
Vue.use(require('vue-moment'));

// Install BootstrapVue
Vue.use(BootstrapVue);
Vue.use(VueTippy);
Vue.use(Snotify, {
    global: {
        newOnTop: false,
    },
    toast: {
        position: "centerBottom",
        icon: false,
        showProgressBar: false,
        timeout: 1000,
    }
});

/**
 * The following block of code may be used to automatically register your
 * Vue mixins. It will recursively scan this directory for the Vue
 * mixins and automatically register them with their "basename".
 *
 * Eg. ./mixins/ExampleComponent.vue -> <example-mixins></example-mixins>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.mixins(key.split('/').pop().split('.')[0], files(key).default));

/**
 * Third Party mixins
 */
Vue.component('pagination', require('laravel-vue-pagination'));
Vue.component('font-awesome-icon', FontAwesomeIcon);
Vue.component("tippy", TippyComponent);

/**
 * Application mixins
 */
Vue.component('passport-clients', require('./components/Settings/OauthClients.vue').default);
Vue.component('passport-authorized-clients', require('./components/Settings/AuthorizedClients.vue').default);
Vue.component('passport-personal-access-tokens', require('./components/Settings/PersonalAccessTokens.vue').default);
Vue.component('create-topic', require('./components/misc/CreateTopic.vue').default);
Vue.component('subscribe-topic', require('./components/misc/SubscribeTopic.vue').default);
Vue.component('missing-table', require('./components/misc/Missing.vue').default);
Vue.component('products-table', require('./components/ProductsPage.vue').default);
Vue.component('orders-table', require('./components/OrdersPage.vue').default);
Vue.component('api2cart-configuration', require('./components/Settings/Api2cartConnections.vue').default);
Vue.component('rmsapi-configuration', require('./components/Settings/RmsapiiConfiguration.vue').default);
Vue.component('packlist-configuration-modal', require('./components/Packlist/FiltersModal.vue').default);
Vue.component('packsheet-page', require('./components/PacksheetPage.vue').default);
Vue.component('packlist-table-entry', require('./components/Packlist/PacklistEntry.vue').default);
Vue.component('apt-configuration-modal', require('./components/Widgets/APT/ConfigurationModal.vue').default);
Vue.component('user-table', require('./components/UsersPage.vue').default);
Vue.component('printnode-configuration', require('./components/Settings/PrintNode.vue').default);
Vue.component('printer-configuration', require('./components/Settings/PrintersConfiguration.vue').default);
Vue.component('picks-table', require('./components/PicklistPage.vue').default);
Vue.component('api', require('./mixins/api').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding mixins to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app'
});
