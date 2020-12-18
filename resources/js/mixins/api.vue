<template>

</template>

<script>
    export default {
    name: "api",

    methods: {
        getOrderProducts: function(params) {
            return axios.get('/api/order/products', {params: params})
        },
    }
}
</script>

<style scoped>

</style>
