<template>
    <div>
        <div class="text-primary h5">{{ product['name'] }}</div>
        <div>
            sku: <font-awesome-icon icon="copy" class="fa-xs btn-link" role="button" @click="copyToClipBoard(product['sku'])"></font-awesome-icon>
            <b> <a target="_blank" :href="'/products?sku=' + product['sku'] " class="font-weight-bold">{{ product['sku'] }}</a></b>
        </div>
        <div>
            <template v-for="tag in product.tags">
                <a class="badge text-uppercase" :key="tag.id" :href="'products?has_tags=' + tag.name"> {{ tag.name }} </a>
            </template>
        </div>
    </div>
</template>

<script>
    import helpers from "../../mixins/helpers";

    export default {
        mixins: [helpers],

        name: "ProductInfoCard",

        props: {
            product: null,
        },
    }
</script>

<style scoped>

</style>
