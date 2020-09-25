<template>
    <div class="row  align-content-center   ">
        <div class="col-lg-6">
            <div class="text-primary h4">{{ entry['name_ordered'] }}</div>
            <div class="text-secondary h5">sku ordered: <span class="font-weight-bold"> {{ entry['sku_ordered'] }} </span></div>
            <div class="text-secondary h5">
                product:
                <span class="font-weight-bold">
                    <a target="_blank" :href="productUrl">{{ productSku }}</a>
                </span>
            </div>
        </div>
        <div class="col-lg-6 text-center">
            <div class="row">
                <div class="col-3" v-bind:class="{ 'bg-warning': Math.ceil(entry['quantity_ordered']) > 1 }">
                    <div class="small">ordered</div>
                    <div class="h3">{{ dashIfZero(Math.ceil(entry['quantity_ordered'])) }}</div>
                </div>
                <div class="col-3">
                    <div class="small">picked</div>
                    <div class="h3">{{ dashIfZero(Math.ceil(entry['quantity_picked'])) }}</div>
                </div>
                <div class="col-3">
                    <div class="small">shipped</div>
                    <div class="h3">{{ dashIfZero(Math.ceil(entry['quantity_shipped'])) }}</div>
                </div>
                <div class="col-3">
                    <div class="small">shelf</div>
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
        },
        methods: {
            dashIfZero(value) {
                return value === 0 ? '-' : value;
            },
        }
    }
</script>

<style scoped>

</style>
