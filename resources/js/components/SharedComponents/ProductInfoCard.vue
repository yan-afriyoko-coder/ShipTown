<template>
    <div>
        <div class="text-primary h5">{{ product['name'] }}</div>
        <div>
            sku: <a target="_blank" :href="'/products?sku=' + product['sku'] " class="font-weight-bold">{{ product['sku'] }}</a>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ProductInfoCard",

        props: {
            product: null,
        },

        computed: {
            // labelFormatted() {
            //     return this.label || '';
            // },
            // numberFormatted() {
            //     return this.dashIfZero(Number(this.number || 0));
            // },
        },

        methods: {
            // dashIfZero(value) {
            //     return value === 0 ? '-' : value;
            // },
        },
    }
</script>

<style scoped>

</style>
