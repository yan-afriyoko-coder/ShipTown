<template>

</template>

<script>
export default {
        name: "api",

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
                return axios.get('api/settings/modules/printnode/clients', {params: params})
            },

            apiPostModulePrintnodeClients: function (data) {
                return axios.post('api/settings/modules/printnode/clients', data);
            },

            apiGetApi2cartConnections: function (params) {
                return axios.get('/api/settings/modules/api2cart/connections', {params: params});
            },

            apiDeleteApi2cartConnection: function (connection_id) {
                return axios.delete(`/api/settings/modules/api2cart/connections/${connection_id}`);
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

            apiGetConfigurationAutopilot: function (variable) {
                return axios.get(`api/configuration/` + variable);
            },

            apiPostAutoPilotConfiguration: function (data) {
                return axios.post(`api/configuration`, data);
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

            apiGetPrintNodePrinters: function () {
                return axios.get('/api/settings/modules/printnode/printers');
            },

            apiPostPrintnodePrintJob: function (data) {
                return axios.post('/api/settings/modules/printnode/printjobs', data);
            },

            apiPostRmsapiConnections: function (data) {
                return axios.post('/api/settings/modules/rms_api/connections', data);
            },

            apiGetRmsapiConnections: function () {
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
        }
    }
</script>

<style scoped>

</style>
