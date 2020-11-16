<template>
    <div class="row card mb-3 ml-1 mr-1" >
        <div class="col p-2 pl-3">
            <div class="row text-left" @click="toggle">
                <div class="col-md-6">
                    <div class="text-primary h4">{{ product.name }}</div>
                    <div class="text-secondary h5">sku: <span class="font-weight-bold"> {{ product.sku }} </span></div>
                </div>
                <div class="col-md-6">
                    <div class="row small font-weight-bold text-right">
                        <div class="col-2 text-left">Location</div>
                        <div class="col-2 d-none d-md-block ">In Stock</div>
                        <div class="col-3 col-md-2">Reserved</div>
                        <div class="col-3">Available</div>
                        <div class="col-4 col-md-3">Shelf</div>
                    </div>
                    <div class="row text-right" v-for="warehouse_inventory in product.inventory">
                        <div class="col-2 text-left">{{ warehouse_inventory.location_id }}</div>
                        <div class="col-2 d-none d-md-block ">{{ warehouse_inventory.quantity | numberFormat }}</div>
                        <div class="col-3 col-md-2">{{ warehouse_inventory.quantity_reserved | numberFormat }}</div>
                        <div class="col-3">{{ warehouse_inventory.quantity - warehouse_inventory.quantity_reserved | numberFormat }}</div>
                        <div class="col-4 col-md-3">{{ warehouse_inventory.shelve_location }}</div>
                    </div>
                    <div class="row text-right font-weight-bold">
                        <div class="col-2"></div>
                        <div class="col-2 d-none d-md-block ">{{ product.quantity | numberFormat }}</div>
                        <div class="col-3 col-md-2">{{ product.quantity_reserved | numberFormat }}</div>
                        <div class="col-3">{{ product.quantity - product.quantity_reserved | numberFormat }}</div>
                        <div class="col-4 col-md-3 text-primary h2 mb-0 pr-2 ">
                            +
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" v-if="showOrders">
                <div class="col">

                    <template v-for="orderProduct in orderProducts">
                       <div>
                           <hr>
                           <div class="row text-left mb-2">
                               <div class="col-6">
                                   <div class="h5">
                                       <a target="_blank" :href="getProductLink(orderProduct)">
                                           #{{ orderProduct['order']['order_number']}}
                                       </a>
                                   </div>
                                   <div class="">
                                       {{ orderProduct['order']['status_code']}}
                                   </div>
                               </div>
                               <div class="col-6">
                                   <div class="row text-center">
                                       <div class="col-4">
                                           <div class="small">ordered</div>
                                           <div class="h3">{{ Math.ceil(orderProduct['quantity_ordered']) }}</div>
                                       </div>
                                       <div class="col-4">
                                           <div class="small">picked</div>
                                           <div class="h3">{{ dashIfZero(Number(orderProduct['quantity_picked'])) }}</div>
                                       </div>
                                       <div class="col-4">
                                           <div class="small">shipped</div>
                                           <div class="h3">{{ dashIfZero(Number(orderProduct['quantity_shipped']))  }}</div>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                    </template>

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


        mounted() {

        },

        data: function() {
            return {
                showOrders: false,
                orderProducts: null,
            };
        },

        filters: {
            numberFormat: (x) => {
                x = parseInt(x).toString();

                if (x == '0') return '-';

                var pattern = /(-?\d+)(\d{3})/;
                while (pattern.test(x)) x = x.replace(pattern, "$1 $2");
                return x;
            }
        },

        methods: {
            toggle() {
                this.showOrders = !this.showOrders;

                if (this.showOrders) {
                    this.loadOrders();
                }
            },

            loadOrders: function () {
                const params = {
                    'filter[product_id]': this.product['id'],
                    'sort': '-id',
                    'include': 'order'
                }

                axios.get('/api/order/products', {params: params})
                    .then(({data}) => {
                        this.orderProducts = data.data
                    });
            },
            dashIfZero(value) {
                return value === 0 ? '-' : value;
            },
            showHideProducts() {
                this.showProducts = ! this.showProducts;
            },
            getProductLink(orderProduct) {
                return '/orders?search=' + orderProduct['order']['order_number'];
            },
            getProductQuantity(orderProduct) {
                return orderProduct['product'] ? Number(orderProduct['product']['quantity']) : -1;
            },
            ifHasEnoughStock(orderProduct) {
                return this.getProductQuantity(orderProduct) < Number(orderProduct['quantity_ordered']);
            }
        }
    }
</script>

<style scoped>

</style>
