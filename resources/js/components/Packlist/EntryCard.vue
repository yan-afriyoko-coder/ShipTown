<template>
    <div class="row">
        <div class="col-lg-6">
            <div class="text-primary h5">{{ entry['name_ordered'] }}</div>
            <div>
                sku ordered: <b>{{ entry['sku_ordered'] }}</b>
            </div>
            <div>
                product: <b><a target="_blank" :href="productUrl">{{ productSku }}</a></b>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <div class="col-6 small">
                    <div >
                        ordered: <b>{{ dashIfZero(Math.ceil(entry['quantity_ordered'])) }}</b>
                    </div>
                    <div>
                        picked: <b>{{ dashIfZero(Math.ceil(entry['quantity_picked'])) }}</b>
                    </div>
                    <div>
                        shipped: <b>{{ dashIfZero(Math.ceil(entry['quantity_shipped'])) }}</b>
                    </div>
                    <div>
                        inventory: <b>{{ dashIfZero(Math.ceil(productInventory)) }}</b>
                    </div>
                </div>
                <div class="col-3 text-center" v-bind:class="{ 'bg-warning': Math.ceil(entry['quantity_ordered']) > 1 }">
                    <small>to ship</small>
                    <h3>{{ dashIfZero(Math.ceil(entry['quantity_ordered'] - entry['quantity_shipped'])) }}</h3>
                </div>
                <div class="col-3 text-center">
                    <small>shelf</small>
                    <h3>{{ entry['inventory_source_shelf_location'] }}</h3>
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
