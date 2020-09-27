<template>
    <div class="row card mb-3 ml-1 mr-1" >
        <div class="col p-2 pl-3">
            <div class="row text-left" @click="toggle">
                <div class="col-md-6">
                    <div class="text-primary h4">{{ product.name }}</div>
                    <div class="text-secondary h5">sku: <span class="font-weight-bold"> {{ product.sku }} </span></div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-2 small font-weight-bold">Location</div>
                        <div class="col-2 small font-weight-bold text-right">In Stock</div>
                        <div class="col-2 small font-weight-bold text-right">Reserved</div>
                        <div class="col-3 small font-weight-bold text-right">Available</div>
                        <div class="col-3 small font-weight-bold text-right">Shelf</div>
                    </div>
                    <div class="row" v-for="warehouse_inventory in product.inventory">
                        <div class="col-2">{{ warehouse_inventory.location_id }}</div>
                        <div class="col-2 text-right">{{ warehouse_inventory.quantity | numberFormat }}</div>
                        <div class="col-2 text-right">{{ warehouse_inventory.quantity_reserved | numberFormat }}</div>
                        <div class="col-3 text-right">{{ warehouse_inventory.quantity - warehouse_inventory.quantity_reserved | numberFormat }}</div>
                        <div class="col-3 text-right">{{ warehouse_inventory.shelve_location }}</div>
                    </div>
                    <div class="row">
                        <div class="col-2"></div>
                        <div class="col-2 text-right font-weight-bold">{{ product.quantity | numberFormat }}</div>
                        <div class="col-2 text-right font-weight-bold">{{ product.quantity_reserved | numberFormat }}</div>
                        <div class="col-3 text-right font-weight-bold">{{ product.quantity - product.quantity_reserved | numberFormat }}</div>
                        <div class="col-3 text-right text-primary h2 mb-0 pr-2 ">
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
                console.log('togling');
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
                        console.log(data.data);
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
