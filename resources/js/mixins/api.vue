<template>

</template>

<script>
export default {
        name: "api",

        methods: {
            apiGetActivityLog: function (params) {
                return axios.get('/api/logs', {params: params});
            },

            apiGetProducts: function(params) {
                return axios.get('/api/products', {params: params});
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
            }
        }
    }
</script>

<style scoped>

</style>
