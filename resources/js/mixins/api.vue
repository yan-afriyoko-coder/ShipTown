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

            apiGetOrderProducts: function(params) {
                return axios.get('/api/order/products', {params: params});
            },

            apiGetOrderComments: function(params) {
                return axios.get('/api/order/comments', {params: params});
            },

            apiGetOrderShipments: function(params) {
                return axios.get('/api/order/shipments', {params: params});
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

            apiGetUsers: function (userId) {
                return axios.get(`/api/admin/users/${userId}`);
            },

            apiPostUserUpdate: function (userId, data) {
                return axios.put(`/api/admin/users/${userId}`, data);
            },

            apiGetModulePrintnodeClients: function (params) {
                return axios.get('api/settings/modules/printnode/clients', {params: params})
            },

            apiPostModulePrintnodeClients: function (data) {
                return axios.post('api/settings/modules/printnode/clients', data);
            },
        }
    }
</script>

<style scoped>

</style>
