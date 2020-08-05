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
import { faCog, faQuestionCircle, faUserEdit, faBarcode } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import VueTippy, { TippyComponent } from "vue-tippy";
import Snotify from 'vue-snotify';

library.add(faCog);
library.add(faQuestionCircle);
library.add(faUserEdit);
library.add(faBarcode);

Vue.config.productionTip = false

Vue.use(Loading);
Vue.use(require('vue-moment'));
// Install BootstrapVue
Vue.use(BootstrapVue);
Vue.use(VueTippy);
Vue.use(Snotify);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

/**
 * Third Party components
 */
Vue.component('pagination', require('laravel-vue-pagination'));
Vue.component('font-awesome-icon', FontAwesomeIcon);
Vue.component("tippy", TippyComponent);

/**
 * Application components
 */
Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue').default
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue').default
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue').default
);

Vue.component(
    'create-topic',
    require('./components/CreateTopic.vue').default
);

Vue.component(
    'subscribe-topic',
    require('./components/SubscribeTopic.vue').default
);

Vue.component(
    'missing-table',
    require('./components/Missing.vue').default
);

Vue.component(
    'products-table',
    require('./components/Products/List.vue').default
);

Vue.component(
    'api2cart-configuration',
    require('./components/Api2CartConfiguration.vue').default
);

Vue.component(
    'rmsapi-configuration',
    require('./components/RMSApiConfiguration.vue').default
);

Vue.component(
    'picklist-table',
    require('./components/Picklist/List.vue').default
);

Vue.component(
    'picklist-configuration-modal',
    require('./components/Picklist/ConfigurationModal.vue').default
);

Vue.component(
    'apt-configuration-modal',
    require('./components/Widgets/APT/ConfigurationModal.vue').default
);

Vue.component(
    'user-table',
    require('./components/Users/List.vue').default
);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
