<template>

    <div>
        <div class="row card mb-3 ml-1 mr-1" >
            <div class="col p-2 pl-3">
                <div class="row text-left">
                    <div class="col-md-6">
                        <div class="text-primary h5">{{ product.name }}</div>
                        <div>sku: <b> <a :href="'/products?search=' + product['sku']">{{ product['sku'] }}</a></b></div>
                        <div>
                            <template v-for="tag in product.tags">
                                <a class="badge text-uppercase" :href="'products?has_tags=' + Object.values(tag.name)[0]"> {{ Object.values(tag.name)[0] }} </a>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" @click="toggle">
                        <div class="row small font-weight-bold text-right">
                            <div class="col-2 text-left">Location</div>
                            <div class="col-2 d-none d-sm-block ">In Stock</div>
                            <div class="col-3 col-sm-2">Reserved</div>
                            <div class="col-3">Available</div>
                            <div class="col-4 col-sm-3">Shelf</div>
                        </div>
                        <template v-for="warehouse_inventory in product.inventory">
                            <div class="row text-right" >
                                <div class="col-2 text-left">{{ warehouse_inventory.location_id }}</div>
                                <div class="col-2 d-none d-sm-block">{{ warehouse_inventory.quantity | numberFormat }}</div>
                                <div class="col-3 col-sm-2">{{ warehouse_inventory.quantity_reserved | numberFormat }}</div>
                                <div class="col-3">{{ warehouse_inventory.quantity - warehouse_inventory.quantity_reserved | numberFormat }}</div>
                                <div class="col-4 col-sm-3">{{ warehouse_inventory.shelve_location }}</div>
                            </div>
                        </template>
                        <div class="row text-right font-weight-bold">
                            <div class="col-2"></div>
                            <div class="col-2 d-none d-sm-block ">{{ product.quantity | numberFormat }}</div>
                            <div class="col-3 col-sm-2">{{ product.quantity_reserved | numberFormat }}</div>
                            <div class="col-3">{{ product.quantity - product.quantity_reserved | numberFormat }}</div>
                        </div>
                    </div>
                </div>


                <div v-if="!showOrders" @click="toggle" class="row text-center text-secondary" >
                    <div class="col">
                        <font-awesome-icon icon="chevron-down" class="fa fa-xs"></font-awesome-icon>
                    </div>
                </div>

                <div v-else @click="toggle" class="row text-center">
                    <div class="col">
                        <font-awesome-icon icon="chevron-up" class="fa fa-xs text-secondary"></font-awesome-icon>
                    </div>
                </div>

                <div class="row" v-if="showOrders">
                    <div class="col">
                        <div class="row tabs-container mb-2">
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link p-0 pl-2 pr-2 active" @click.prevent="currentTab = 'pendingOrders'" data-toggle="tab" href="#t">
                                        Pending Orders
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link p-0 pl-2 pr-2"  @click.prevent="currentTab = 'activityLog'" data-toggle="tab" href="#">
                                        Activity Log
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a v-if="sharingAvailable()" @click.prevent="shareLink" class="nav-link p-0 pl-2 pr-2" href="#">
                                        <font-awesome-icon icon="share-alt" class="fas fa-sm"></font-awesome-icon>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div>
                            {{ statusMessage }}
                        </div>

                        <template v-if="currentTab === 'pendingOrders'" v-for="orderProduct in orderProducts">
                           <div>
                               <div class="row text-left mb-2">
                                   <div class="col-4">
                                       <div>
                                           <a target="_blank" :href="getProductLink(orderProduct)">
                                               #{{ orderProduct['order']['order_number']}}
                                           </a>
                                       </div>
                                       <div class="small">
                                           {{ orderProduct['order']['order_placed_at'] | moment('MMM DD')  }}
                                       </div>
                                       <div class="small">
                                           {{ orderProduct['order']['status_code']}}
                                       </div>
                                   </div>
                                   <div class="col-8">
                                       <div class="row justify-content-end text-center">
                                           <div class="col">
                                               <small>ordered</small>
                                               <h3>{{ Math.ceil(orderProduct['quantity_ordered']) }}</h3>
                                           </div>
                                           <div class="col">
                                               <small>picked</small>
                                               <h3>{{ dashIfZero(Number(orderProduct['quantity_picked'])) }}</h3>
                                           </div>
                                           <div class="col">
                                               <small>skipped</small>
                                               <h3>{{ dashIfZero(Number(orderProduct['quantity_skipped_picking'])) }}</h3>
                                           </div>
                                           <div class="col d-none d-sm-block">
                                               <small>shipped</small>
                                               <h3>{{ dashIfZero(Number(orderProduct['quantity_shipped']))  }}</h3>
                                           </div>
                                           <div class="col">
                                               <small>to ship</small>
                                               <h3>{{ dashIfZero(Number(orderProduct['quantity_to_ship']))  }}</h3>
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <hr>
                           </div>
                        </template>

                        <template  v-if="currentTab === 'activityLog'" v-for="activity in activityLog">
                            <div class="row text-secondary h6">
                                {{ activity['created_at'] | moment('MMM DD @ H:mm')  }} <b> {{ activity['causer'] === null ? 'AutoPilot' : activity['causer']['name'] }}</b> {{ activity['description'] }} {{ activity['changes'] }}
                            </div>
                        </template>

                    </div>
                </div>

            </div>

        </div>

        <div class="spacer-bottom row mb-4 mt-4" v-if="showOrders"></div>

    </div>
</template>

<script>
    import api from "../../mixins/api";

    export default {
        name: "ProductCard",

        mixins: [api],

        props: {
            product: Object,
            expanded: false,
        },

        watch: {
            currentTab() {
                switch (this.currentTab) {
                    case 'pendingOrders':
                        this.loadOrders();
                        break;
                    case 'activityLog':
                        this.loadActivityLog();
                        break;
                    default:
                        break;
                }
            }
        },

        data: function() {
            return {
                statusMessage: 'No orders found',
                activityLog: null,
                currentTab: '',
                showOrders: false,
                orderProducts: null,
            };
        },

        created: function () {
            if (this.expanded) {
                this.toggle();
            }
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
            sharingAvailable() {
                return navigator.share;
            },

            shareLink() {
                navigator.share({
                    url: '/products?search=' + this.product['sku'],
                    title: document.title
                });
            },

            toggle() {
                this.showOrders = !this.showOrders;

                if ((this.showOrders) && (this.currentTab === '')) {
                    this.currentTab = 'pendingOrders';
                }
            },

            loadOrders: function () {
                this.statusMessage = 'Loading orders ...'
                const params = {
                    'filter[product_id]': this.product['id'],
                    'filter[has_stock_reserved]': true,
                    'sort': 'id',
                    'include': 'order'
                }
                this.apiGetOrderProducts(params)
                    .then(({data}) => {
                        this.statusMessage = '';
                        this.orderProducts = data.data;
                        if (this.orderProducts.length === 0) {
                            this.statusMessage = 'No orders found';
                        }
                    });
            },

            loadActivityLog: function () {
                const params = {
                    'filter[subject_type]': 'App\\Models\\Product',
                    'filter[subject_id]': this.product['id'],
                    'sort': '-id',
                    'include': 'causer'
                }

                this.apiGetActivityLog(params)
                    .then(({data}) => {
                        this.activityLog = data.data
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
li {
    margin-right: 5px;
}
.badge {
    font-family: "Lato",-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",sans-serif;
}
</style>
