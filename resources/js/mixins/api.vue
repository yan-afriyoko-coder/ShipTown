<template>

</template>

<script>
import Vue from "vue";

export default {
        name: "api",

        data: function () {
            return {
                user: null,
                loading_user: false,
            }
        },

        mounted() {
            if (Vue.prototype.$user) {
                this.user = Vue.prototype.$user;
            } else if (Vue.prototype.$user === undefined && Vue.prototype.$loading_user === undefined) {
                Vue.prototype.$loading_user = true;

                this.apiGetUserMe()
                    .then( ({data}) => {
                        Vue.prototype.$user = data.data;
                        this.user = Vue.prototype.$user;
                        console.log(Vue.prototype.$user, Vue.prototype.$loading_user);
                    });
            }
        },

        methods: {
            apiGetUserMe: function () {
                return axios.get('/api/settings/user/me');
            },

            apiGetActivityLog: function (params) {
                return axios.get('/api/logs', {params: params});
            },

            apiGetProducts: function(params) {
                return axios.get('/api/products', {params: params});
            },

            apiKickProduct: function(sku) {
              return axios.get('/products/' + sku  + '/kick/', {params: null});
            },

            apiModuleEcommerceProductInfo: function(params) {
                return axios.get('/api/settings/modules/api2cart/products', {params: params});
            },

            apiGetOrders: function(params) {
                return axios.get('/api/orders', {params: params});
            },

            apiUpdateOrder: function (order_id, data) {
                return axios.put('/api/orders/' + order_id, data);
            },

            apiGetOrderProducts: function(params) {
                return axios.get('/api/order/products', {params: params});
            },

            apiPostOrderProductShipment: function (data) {
                return axios.post('/api/orders/products/shipments', data);
            },

            apiUpdateOrderProduct: function (orderProduct_id, data) {
                return axios.put('/api/order/products/' + orderProduct_id, data);
            },

            apiGetOrderComments: function(params) {
                return axios.get('/api/order/comments', {params: params});
            },

            apiGetOrderShipments: function(params) {
                return axios.get('/api/order/shipments', {params: params});
            },

            apiPostOrderShipment: function (data) {
                return axios.post('/api/order/shipments', data);
            },

            apiPostOrderComment: function (data) {
                return axios.post('/api/order/comments', data);
            },

            apiGetOrderActivities: function (params) {
                return axios.get('/api/logs', {params: params});
            },

            apiPostUserMe: function (data) {
                return axios.post('/api/settings/user/me', data);
            },

            apiGetUser: function (userId) {
                return axios.get(`/api/admin/users/${userId}`);
            },

            apiGetUsers: function (params) {
                return axios.get(`/api/admin/users`, {params: params});
            },

            apiPostUserStore: function (data) {
                return axios.post(`/api/admin/users`, data);
            },

            apiPostUserUpdate: function (userId, data) {
                return axios.put(`/api/admin/users/${userId}`, data);
            },

            apiDeleteUser: function (id) {
                return axios.delete(`/api/admin/users/${id}`);
            },

            apiGetUserRoles: function (params) {
                return axios.get('/api/admin/user/roles', {params: params});
            },

            apiGetModulePrintnodeClients: function (params) {
                return axios.get('/api/settings/modules/printnode/clients', {params: params})
            },

            apiPostModulePrintnodeClients: function (data) {
                return axios.post('/api/settings/modules/printnode/clients', data);
            },

            apiDeletePrintnodeClient: function (id) {
                return axios.delete(`/api/settings/modules/printnode/clients/${id}`, {});
            },

            apiGetApi2cartConnections: function (params) {
                return axios.get('/api/settings/modules/api2cart/connections', {params: params});
            },

            apiDeleteApi2cartConnection: function (connection_id) {
                return axios.delete(`/api/settings/modules/api2cart/connections/${connection_id}`);
            },

            apiGetDpdConfiguration: function () {
                return axios.get(`/api/settings/modules/dpd-ireland/connections`);
            },

            apiPostDpdConfiguration: function (data) {
                return axios.post(`/api/settings/modules/dpd-ireland/connections`, data);
            },

            apiDeleteDpdConfiguration: function (id) {
                return axios.delete(`/api/settings/modules/dpd-ireland/connections/${id}`);
            },

            apiGetOauthTokens: function () {
                return axios.get('/oauth/tokens');
            },

            apiDeleteOauthToken: function (token) {
                return axios.delete('/oauth/tokens/' + token.id);
            },

            apiGetOauthClients: function () {
                return axios.get('/oauth/clients');
            },

            apiGetModuleAutoStatusPickingConfiguration: function () {
                return axios.get(`/api/modules/autostatus/picking/configuration`);
            },

            apiSetModuleAutoStatusPickingConfiguration: function (configuration) {
                return axios.post(`/api/modules/autostatus/picking/configuration`, configuration);
            },

            apiGetRunHourlyJobs: function () {
                return axios.get('/api/run/hourly/jobs');
            },

            apiGetRunDailyJobs: function () {
                return axios.get('/api/run/daily/jobs');
            },

            apiGetRunSync: function () {
                return axios.get('/api/run/sync');
            },

            apiGetRunSyncApi2cart: function () {
                return axios.get('/api/run/sync/api2cart');
            },

            apiGetPrintNodePrinters: function () {
                return axios.get('/api/settings/modules/printnode/printers');
            },

            apiPostPrintnodePrintJob: function (data) {
                return axios.post('/api/settings/modules/printnode/printjobs', data);
            },

            apiPostRmsapiConnections: function (data) {
                return axios.post('/api/settings/modules/rms_api/connections', data);
            },

            apiGetRmsapiConnections: function (params) {
                return axios.get('/api/settings/modules/rms_api/connections', {params: params});
            },

            apiDeleteRmsapiConnection: function (connection_id) {
                return axios.delete(`/api/settings/modules/rms_api/connections/${connection_id}`);
            },

            apiPostApi2cartConnection: function (data) {
                return axios.post('/api/settings/modules/api2cart/connections', data);
            },

            apiPostUserInvite: function (data) {
                return axios.post('/api/admin/user/invites', data);
            },

            apiPostWidget: function (data) {
                return axios.post('/api/widgets', data);
            },

            apiPutWidget: function (widget_id, data) {
                return axios.put('/api/widgets/' + widget_id, data);
            },

            apiGetPacklistOrder: function (params) {
                return axios.get('/api/packlist/order', {params: params});
            },

            apiPrintLabel: function (orderNumber, template) {
                return axios.put(`/api/print/order/${orderNumber}/${template}`);
            },

            apiGetPicklist: function (params) {
                return axios.get('/api/picklist', {params: params});
            },

            apiPostPicklistPick: function (data) {
                return axios.post('/api/picklist/picks', data);
            },

            apiGetModules: function () {
                return axios.get('/api/settings/modules');
            },

            apiToggleModules: function (id) {
                return axios.put('/api/settings/modules/' + id);
            },

            apiGetOrderStatus: function () {
                return axios.get('/api/order-statuses');
            },

            apiPostOrderStatus: function (params) {
                return axios.post('/api/settings/order-statuses', params);
            },

            apiPutOrderStatus: function (id, params) {
                return axios.put('/api/settings/order-statuses/' + id, params);
            },

            apiGetMailTemplate: function () {
                return axios.get('/api/settings/mail-templates');
            },

            apiPutMailTemplate: function (id, params) {
                return axios.put('/api/settings/mail-templates/' + id, params);
            },

            apiGetNavigationMenu: function () {
                return axios.get('/api/settings/navigation-menu');
            },

            apiPostNavigationMenu: function (params) {
                return axios.post('/api/settings/navigation-menu/', params);
            },

            apiPutNavigationMenu: function (id, params) {
                return axios.put('/api/settings/navigation-menu/' + id, params);
            },

            apiDeleteNavigationMenu: function (id) {
                return axios.delete('/api/settings/navigation-menu/' + id);
            },

            apiGetAutomationConfig: function() {
                return axios.get('/api/settings/modules/automations/config');
            },

            apiGetAutomations: function () {
                return axios.get('/api/settings/modules/automations');
            },

            apiShowAutomations: function (id) {
                return axios.get('/api/settings/modules/automations/' + id);
            },

            apiPostAutomations: function (params) {
                return axios.post('/api/settings/modules/automations/', params);
            },

            apiPutAutomations: function (id, params) {
                return axios.put('/api/settings/modules/automations/' + id, params);
            },

            apiDeleteAutomations: function (id) {
                return axios.delete('/api/settings/modules/automations/' + id);
            },

            apiGetWarehouse: function () {
                return axios.get('/api/settings/warehouses');
            },

            apiPostWarehouse: function (params) {
                return axios.post('/api/settings/warehouses/', params);
            },

            apiPutWarehouse: function (id, params) {
                return axios.put('/api/settings/warehouses/' + id, params);
            },

            apiDeleteWarehouse: function (id) {
                return axios.delete('/api/settings/warehouses/' + id);
            },

            apiGetConfiguration: function () {
                return axios.get('/api/settings/configurations/');
            },

            apiSaveConfiguration: function (params) {
                return axios.post('/api/settings/configurations/', params);

            },
        }
    }
</script>

<style scoped>

</style>
