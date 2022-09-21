<template>

</template>

<script>
import Vue from "vue";

export default {
        name: "api",

        data: function () {
            return {
                user: null,
            }
        },

        created() {
            Vue.prototype.$currentUser = JSON.parse(document.querySelector("meta[name='current-user']").getAttribute('content'));
        },

        methods: {
            currentUser: function () {
                return Vue.prototype.$currentUser;
            },

            apiGet(url, params) {
                return axios.get(url, params)
            },

            apiPost(url, data) {
                return axios.post(url, data)
            },

            displayApiCallError: function (error) {
                console.log('API failed call response', error.response);

                if (error.response.status === 400) {
                    this.notifyError(JSON.stringify(error.response.data));
                    return;
                }

                if (error.response.status === 422) {
                    this.notifyError(JSON.stringify(error.response.data));
                    return;
                }

                this.notifyError('API call failed: ' + error.response.status + ' ' + error.response.statusText);
            },

            apiGetUserMe: function () {
                return axios.get('/api/settings/user/me');
            },

            apiGetActivityLog: function (params) {
                return axios.get('/api/activities', {params: params});
            },

            apiActivitiesPost: function (data) {
                return axios.post('/api/activities', data);
            },

            apiGetProducts: function(params) {
                return axios.get('/api/products', {params: params});
            },

            apiGetStocktakeSuggestions: function(params) {
                return this.apiGet('/api/stocktake-suggestions', {params: params});
            },

            apiGetInventory(params) {
                return axios.get('/api/product/inventory', {params: params});
            },

            apiGetRestocking(params) {
                return axios.get('/api/restocking', {params: params});
            },

            apiPostInventory(data) {
                return this.apiPost('/api/product/inventory', data);
            },

            apiGetInventoryMovements(params) {
                return this.apiGet('/api/inventory-movements', {params: params});
            },

            apiPostInventoryMovement(data) {
                return axios.post('/api/inventory-movements', data);
            },

            apiPostStocktakes(data) {
                return axios.post('/api/stocktakes', data);
            },

            apiPostCsvImport(data) {
                return axios.post('/api/csv-import', data);
            },

            apiPostCsvImportDataCollections(data) {
                return axios.post('/api/csv-import/data-collections', data);
            },

            apiPostDataCollection(data) {
                return axios.post('/api/data-collector', data);
            },

            apiUpdateDataCollection(id, data) {
                return axios.put("/api/data-collector/" + id, data);
            },

            apiPostDataCollectorRecords(data) {
                return axios.post('/api/data-collector-records', data);
            },

            apiGetDataCollectorList(params) {
                return axios.get('/api/data-collector', {params: params});
            },

            apiGetDataCollectorRecords(params) {
                return axios.get('/api/data-collector-records', {params: params});
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

            apiPostOrderCheckRequest: function (data) {
                return axios.post('/api/order-check-request', data);
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

            apiPostShipment: function(data) {
                return axios.post('/api/shipments', data);
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

            apiGetDpdUkConnections() {
                return axios.get(`/api/modules/dpd-uk/dpd-uk-connections`);
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
                return axios.get('/api/modules/printnode/printers');
            },

            apiPostPrintnodePrintJob: function (data) {
                return axios.post('/api/modules/printnode/printjobs', data);
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

            apiPostModule: function (id, params) {
                return axios.put('/api/settings/modules/' + id, params);
            },

            apiGetOrderStatus: function (params) {
                return axios.get('/api/order-statuses', {params: params});
            },

            apiGetShippingServices: function (params = {}) {
                return axios.get('/api/shipping-services', {params: params});
            },

            apiPostShippingLabel: function (params) {
                return axios.post('/api/shipping-labels', params);
            },

            apiPostOrderStatus: function (params) {
                return axios.post('/api/settings/order-statuses', params);
            },

            apiPutOrderStatus: function (id, params) {
                return axios.put('/api/settings/order-statuses/' + id, params);
            },

            apiDeleteOrderStatus: function (id, params) {
                return axios.delete('/api/settings/order-statuses/' + id);
            },

            apiGetMailTemplate: function () {
                return axios.get('/api/settings/mail-templates');
            },

            apiPutMailTemplate: function (id, params) {
                return axios.put('/api/settings/mail-templates/' + id, params);
            },

            apiGetNavigationMenu: function (params = {}) {
                return axios.get('/api/settings/navigation-menu', {params: params});
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

            apiRunAutomation: function (id) {
                return axios.post('/api/settings/modules/automations/run', {
                    'automation_id': id
                });
            },

            apiDeleteAutomations: function (id) {
                return axios.delete('/api/settings/modules/automations/' + id);
            },

            apiGetWarehouses: function (params) {
                return axios.get('/api/settings/warehouses', {params: params});
            },

            apiPostWarehouses: function (params) {
                return axios.post('/api/settings/warehouses/', params);
            },

            apiPutWarehouses: function (id, params) {
                return axios.put('/api/settings/warehouses/' + id, params);
            },

            apiDeleteWarehouses: function (id) {
                return axios.delete('/api/settings/warehouses/' + id);
            },

            apiGetConfiguration: function () {
                return axios.get('/api/settings/configurations/');
            },

            apiSaveConfiguration: function (params) {
                return axios.post('/api/settings/configurations/', params);

            },

            apiGetHeartbeats: function () {
                return axios.get('/api/heartbeats/');
            },
        }
    }
</script>

<style scoped>

</style>
