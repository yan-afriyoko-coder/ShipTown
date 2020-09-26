<template>
    <div class="row">
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
                <div class="col-6 text-left">
                    <div class="">ordered: <b>{{ dashIfZero(Math.ceil(entry['quantity_ordered'])) }}</b></div>
                    <div class="">
                        picked:
                        <b>{{ dashIfZero(Math.ceil(entry['quantity_picked'])) }}</b>
                    </div>
                    <div class="">shipped: <b>{{ dashIfZero(Math.ceil(entry['quantity_shipped'])) }}</b></div>
                    <div class="">inventory: <b>{{ dashIfZero(Math.ceil(productInventory)) }}</b></div>
                </div>
                <div class="col-3" v-bind:class="{ 'bg-warning': Math.ceil(entry['quantity_ordered'] - entry['quantity_shipped']) > 1 }">
                    <div class="small">to ship</div>
                    <div class="h3">{{ dashIfZero(Math.ceil(entry['quantity_ordered'] - entry['quantity_shipped'])) }}</div>
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
            productInventory() {
                return this.entry['product'] ? this.entry['product']['quantity'] : 0;
            },
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
