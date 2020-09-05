<template>
    <div class="row mb-3 ml-1 mr-1">
        <div class="col p-2 pl-3">
            <div class="row text-left">
                <div class="col-md-6">
                    <div class="text-primary h4">{{ product.name }}</div>
                    <div class="text-secondary h5">sku: <span class="font-weight-bold"> {{ product.sku }} </span></div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-3 font-weight-bold">Location</div>
                        <div class="col-3 font-weight-bold text-right">In Stock</div>
                        <div class="col-2 font-weight-bold text-right">Reserved</div>
                        <div class="col-2 font-weight-bold text-right">Available</div>
                        <div class="col-2 font-weight-bold text-right">Shelf</div>
                    </div>
                    <div class="row" v-for="warehouse_inventory in product.inventory">
                        <div class="col-3">{{ warehouse_inventory.location_id }}</div>
                        <div class="col-3 text-right">{{ warehouse_inventory.quantity | numberFormat }}</div>
                        <div class="col-2 text-right">{{ warehouse_inventory.quantity_reserved | numberFormat }}</div>
                        <div class="col-2 text-right">{{ warehouse_inventory.quantity - warehouse_inventory.quantity_reserved | numberFormat }}</div>
                        <div class="col-2 text-right">{{ warehouse_inventory.shelve_location }}</div>
                    </div>
<!--                    <div class="row">-->
<!--                        <div class="col-3"></div>-->
<!--                        <div class="col-3 text-right font-weight-bold">{{ product.quantity | numberFormat }}</div>-->
<!--                        <div class="col-2 text-right font-weight-bold">{{ product.quantity_reserved | numberFormat }}</div>-->
<!--                        <div class="col-2 text-right font-weight-bold">{{ product.quantity - product.quantity_reserved | numberFormat }}</div>-->
<!--                        <div class="col-2"></div>-->
<!--                    </div>-->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ProductCard",

        props: {
            product: Object,
        },

        filters: {
            numberFormat: (x) => {
                x = parseInt(x).toString();

                if (x == '0') return '-';

                var pattern = /(-?\d+)(\d{3})/;
                while (pattern.test(x)) x = x.replace(pattern, "$1 $2");
                return x;
            }
        }
    }
</script>

<style scoped>
    .col {
        background-color: #ffffff;
        border: 1px solid #76777838;
    }
</style>
