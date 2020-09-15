<template>
    <div class="row text-left">
        <div class="col-md-8">
            <div class="text-primary h4">{{ entry['name_ordered'] }}</div>
            <div class="text-secondary h5">sku ordered: <span class="font-weight-bold"> {{ entry['sku_ordered'] }} </span></div>
            <div class="text-secondary h5">product: <span class="font-weight-bold"> <a target="_blank" :href="productUrl">{{ productSku }}</a> </span></div>

        </div>
        <div class="col-md-4">
            <div class="row pt-1 mt-1 text-center">
                <div class="col-2 offset-6" v-bind:class="{ 'bg-warning': Math.ceil(entry['quantity_ordered']) > 1 }">
                    <div class="">Qty</div>
                    <div class="h3">{{ Math.ceil(entry['quantity_ordered']) }}</div>
                </div>
                <div class="col-4">
                    <div class="">Shelf</div>
                    <div class="h3">{{ entry['inventory_source_shelf_location'] }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "EntryCard",

        props: {
            entry: Object,
        },
        computed: {
            productSku() {
               return this.entry['product'] ? this.entry['product']['sku'] : '';
            },
            productUrl() {
                return '/products?sku=' + this.productSku;
            }
        }
    }
</script>

<style scoped>

</style>
