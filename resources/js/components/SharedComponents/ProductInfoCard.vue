<template>
    <div>
        <div class="text-primary h5">{{ product['name'] }}</div>
        <div>
            sku: <font-awesome-icon icon="copy" class="fa-xs btn-link" role="button" @click="copyToClipBoard(product['sku'])"></font-awesome-icon>
            <b> <a target="_blank" :href="'/products?sku=' + product['sku'] " class="font-weight-bold">{{ product['sku'] }}</a></b>
        </div>
        <div>
            <template v-for="tag in product['tags']">
                <a class="badge text-uppercase btn btn-outline-primary" :key="tag.id" @click.prevent="setUrlParameterAngGo('filter[product_has_tags]', getTagName(tag))"> {{ getTagName(tag) }} </a>
            </template>
<!--            <template v-for="tag in product.tags">-->
<!--                <a class="badge text-uppercase" :key="tag.id" :href="'products?has_tags=' + getTagName(tag)"> {{ getTagName(tag) }} </a>-->
<!--            </template>-->
        </div>
    </div>
</template>

<script>
    import helpers from "../../mixins/helpers";
    import url from "../../mixins/url";

    export default {
        mixins: [helpers, url],

        name: "ProductInfoCard",

        props: {
            product: null,
        },

        methods: {
            getTagName(tag) {
                return tag.name instanceof Object ? tag.name['en'] : tag.name
            },

            setUrlParameterAngGo: function(param, value) {
                this.setUrlParameter(param, value);
                window.location.reload();
                return this;
            },
        }
    }
</script>

<style scoped>

</style>
