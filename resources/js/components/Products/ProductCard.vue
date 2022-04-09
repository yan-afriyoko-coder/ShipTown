<template>
    <div>
        <div class="row card ml-1 mr-1" >
            <div class="col p-2 pl-3">
                <div class="row">
                    <div class="col">
                        <div class="text-primary h5">{{ product.name }}
                            <a @click="kickProduct" class="text-white">o</a>
<!--                            <div class="float-right" data-toggle="modal" data-target="#filterConfigurationModal">-->
<!--                                <button class="btn btn-success" v-if="quantityOrdered > 0">{{ quantityOrdered }}</button>-->
<!--                                <font-awesome-icon icon="cart-plus" class="btn-link mt-1 mr-1" role="button" />-->
<!--                            </div>-->
                        </div>
                    </div>
                </div>
                <div class="row text-left">
                    <div class="col-md-6">
                        <div>
                            sku:
                            <font-awesome-icon icon="copy" class="fa-xs btn-link" role="button" @click="copyToClipBoard(product['sku'])"></font-awesome-icon>
                            <b> <a :href="'/products?search=' + product['sku']">{{ product['sku'] }}</a></b></div>
                        <div>
                            <template v-for="tag in product.tags">
                                <a class="badge text-uppercase" :key="tag.id" :href="'products?has_tags=' + Object.values(tag.name)[0]"> {{ Object.values(tag.name)[0] }} </a>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" @click="toggle">
                        <div class="row small font-weight-bold text-right">
                            <div class="col-2 text-left">Location</div>
                            <div class="col-2 d-none d-sm-block ">In Stock</div >
                            <div class="col-3 col-sm-2">Reserved</div>
                            <div class="col-3">Available</div>
                            <div class="col-4 col-sm-3">Shelf</div>
                        </div>
                        <template v-for="warehouse_inventory in product.inventory">
                            <div class="row text-right" :key="warehouse_inventory.id">
                                <div class="col-2 text-left">{{ warehouse_inventory.warehouse_code }}</div>
                                <div class="col-2 d-none d-sm-block">{{ warehouse_inventory.quantity | numberFormat }}</div>
                                <div class="col-3  col-sm-2">{{ warehouse_inventory.quantity_reserved | numberFormat }}</div>
                                <div class="col-3">{{ warehouse_inventory.quantity - warehouse_inventory.quantity_reserved | numberFormat }}</div>
                                <div class="col-4 col-sm-3">{{ warehouse_inventory.shelf_location }}</div>
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
                                    <a class="nav-link p-0 pl-2 pr-2 active" @click.prevent="currentTab = 'orders'" data-toggle="tab" href="#t">
                                        Orders
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link p-0 pl-2 pr-2"  @click.prevent="currentTab = 'activityLog'" data-toggle="tab" href="#">
                                        Activity
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link p-0 pl-2 pr-2"  @click.prevent="currentTab = 'prices'" data-toggle="tab" href="#">
                                        Pricing
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link p-0 pl-2 pr-2"  @click.prevent="currentTab = 'inventory'" data-toggle="tab" href="#">
                                        Inventory
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link p-0 pl-2 pr-2"  @click.prevent="currentTab = 'aliases'" data-toggle="tab" href="#">
                                        Aliases
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a v-if="sharingAvailable()" @click.prevent="shareLink" class="nav-link p-0 pl-2 pr-2" href="#">
                                        <font-awesome-icon icon="share-alt" class="fas fa-sm"></font-awesome-icon>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <template v-if="currentTab === 'orders'">
                            <div>
                                {{ statusMessageOrder }}
                            </div>
                            <div v-if="!orders.length">
                                No orders found
                            </div>
                           <div v-for="orderProduct in orders" :key="orderProduct.id">
                               <div class="row text-left mb-2">
                                   <div class="col">
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
                                   <div class="col">
                                       <div class="row justify-content-end text-center">
                                           <div class="cold d-none d-sm-block">
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

                        <template v-if="currentTab === 'activityLog'">
                            <div>
                                {{ statusMessageActivity }}
                            </div>
                          <table>
                            <tr v-for="activity in activityLog" class="container" :key="activity.id">
                              <td class="align-text-top" :title="activity['created_at'] | moment('YYYY-MM-DD H:mm:ss') ">
                                {{ activity['created_at'] | moment('MMM DD')  }} {{ activity['created_at'] | moment('H:mm')  }}:
                              </td>
                              <td>
                                <div class="row">
                                  <b>{{ activity['causer'] === null ? 'AutoPilot' : activity['causer']['name'] }} </b>  &nbsp; {{ activity['description'] }}
                                </div>
                                <div class="flex-row" v-for="(value, name) in activity['properties']['attributes'] ? activity['properties']['attributes'] : activity['properties']">
                                   {{ name }} = {{ value }}
                                </div>
                              </td>
                            </tr>
                          </table>
                        </template>

                        <template v-if="currentTab === 'prices'">
                            <div class="container">
                                <table class="small table table-borderless table-responsive mb-0">
                                    <thead>
                                        <tr>
                                            <th>Location</th>
                                            <th>Price</th>
                                            <th>Sale Price</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="price in product.prices" :key="price.id">
                                            <td>{{ price.location_id }}</td>
                                            <td>{{ price.price }}</td>
                                            <td>{{ price.sale_price }}</td>
                                            <td>{{ price.sale_price_start_date }}</td>
                                            <td>{{ price.sale_price_end_date }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </template>

                        <template v-if="currentTab === 'inventory'">
                            <div class="container">

                                <table class="small table table-borderless table-responsive mb-0">
                                    <thead>
                                        <tr>
                                            <th>Location</th>
                                            <th>Stock</th>
                                            <th>Reserved</th>
                                            <th>Available</th>
                                            <th>Restock Level</th>
                                            <th>Reorder Point</th>
                                            <th>Required</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="inventory in product.inventory" :key="inventory.id">
                                            <td>{{ inventory.warehouse_code }}</td>
                                            <td>{{ inventory.quantity | numberFormat}}</td>
                                            <td>{{ inventory.quantity_reserved | numberFormat}}</td>
                                            <td>{{ inventory.quantity_available | numberFormat}}</td>
                                            <td>{{ inventory.restock_level | numberFormat }}</td>
                                            <td>{{ inventory.reorder_point | numberFormat}}</td>
                                            <td>{{ inventory.quantity_required | numberFormat}}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </template>

                        <template v-if="currentTab === 'aliases'">
                            <div class="container">
                                <span v-for="alias in product.aliases" :key="alias.id">{{ alias.alias }}</span>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

        </div>

        <div class="spacer-bottom row mb-4 mt-4" v-if="showOrders"></div>

        <div class="modal fade widget-configuration-modal" id="filterConfigurationModal" ref="filterConfigurationModal" tabindex="-1" role="dialog" aria-labelledby="picklistConfigurationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Settings: Packlist</h5>
                        <div class="widget-tools-container">
<!--                            <font-awesome-icon icon="question-circle" :content="helpText" v-tippy></font-awesome-icon>-->
                        </div>
                    </div>
                    <div class="modal-body" style="margin: 0 auto 0;">
                        <button type="button" class="btn mb-1 btn-info" @click="orderProduct(0)">0</button><br>
                        <button type="button" class="btn mb-1 btn-info" @click="orderProduct(12)">12</button><br>
                        <button type="button" class="btn mb-1 btn-info" @click="orderProduct(24)">24</button><br>
                        <button type="button" class="btn mb-1 btn-info" @click="orderProduct(36)">36</button><br>
                        <button type="button" class="btn mb-1 btn-info" @click="orderProduct(36)">MORE</button><br>
<!--                        <form method="POST" @submit.prevent="handleSubmit">-->
<!--                            <div class="form-group">-->
<!--                                <label class="form-label" for="selectStatus">Inventory Location ID</label>-->
<!--                                <input v-model="filters['inventory_source_location_id']" type="number" class="form-control" />-->
<!--                            </div>-->

<!--                            <slot name="actions" v-bind:filters="filters"></slot>-->
<!--                        </form>-->
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click.prevent="handleSubmit" class="btn btn-primary">OK</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    import api from "../../mixins/api";
    import helpers from "../../mixins/helpers";

    export default {
        name: "ProductCard",

        mixins: [api, helpers],

        props: {
            product: Object,
            expanded: false,
            ordered: 0,
        },

        watch: {
            currentTab() {
                switch (this.currentTab) {
                    case 'orders':
                        if(!this.activeOrderProducts.length){
                            this.loadActiveOrders();
                            this.loadCompletedOrders(10);
                        }
                        break;
                    case 'activityLog':
                        if(!this.activityLog.length){
                            this.loadActivityLog();
                        }
                        break;
                    default:
                        break;
                }
            }
        },

        data: function() {
            return {
                statusMessageOrder: '',
                statusMessageActivity: '',
                activityLog: [],
                currentTab: '',
                showOrders: false,
                activeOrderProducts: [],
                completeOrderProducts: []
            };
        },

        computed: {
            orders() {
                return this.activeOrderProducts.concat(this.completeOrderProducts)
            },

            quantityOrdered() {
                return this.ordered
            }
        },

        created: function () {
            if (this.expanded) {
                this.toggle();
            }
        },

        filters: {
            numberFormat: (x) => {
                x = parseInt(x).toString();

                if (x ==='0') return '-';

                var pattern = /(-?\d+)(\d{3})/;
                while (pattern.test(x)) x = x.replace(pattern, "$1 $2");
                return x;
            }
        },

        methods: {
            hide() {
                $('#filterConfigurationModal').modal('hide');
            },

            orderProduct(quantity) {
                console.log(this.$ref);
                this.ordered = quantity;
                this.hide();
            },

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
                    this.currentTab = 'orders';
                }
            },

            kickProduct: function () {
                this.apiKickProduct(this.product['sku']);
            },

            loadActiveOrders: function () {
                this.statusMessageOrder = "Loading orders ..."
                const params = {
                    'filter[product_id]': this.product['id'],
                    'filter[order.is_active]': true,
                    'sort': 'id',
                    'include': 'order'
                }
                this.apiGetOrderProducts(params)
                    .then(({data}) => {
                        this.statusMessageOrder = '';
                        this.activeOrderProducts = data.data;
                    });
            },

            loadCompletedOrders: function (count = 5) {
                this.statusMessageOrder = "Loading orders ..."
                const params = {
                    'filter[product_id]': this.product['id'],
                    'filter[order.is_active]': false,
                    'sort': '-id',
                    'include': 'order',
                    'per_page': count
                }
                this.apiGetOrderProducts(params)
                    .then(({data}) => {
                        this.statusMessageOrder = '';
                        this.completeOrderProducts = data.data;
                    });
            },

            loadActivityLog: function () {
                this.statusMessageActivity = "Loading activities ...";
                const params = {
                    'filter[subject_type]': 'App\\Models\\Product',
                    'filter[subject_id]': this.product['id'],
                    'sort': '-id',
                    'include': 'causer',
                    'per_page': 100
                }

                this.apiGetActivityLog(params)
                    .then(({data}) => {
                        this.statusMessageActivity = '';
                        this.activityLog = data.data
                        if (this.activityLog.length === 0) {
                            this.statusMessageActivity = 'No activities found';
                        }
                    });
            },

            dashIfZero(value) {
                return value === 0 ? '-' : value;
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
.table th, .table td {
    padding: 0.25rem;
}

.btn:active{
    background-color: rgb(94, 79, 126);
    border-color:rgb(94, 79, 126);
    box-shadow: 0 1px 1px rgba(255, 255, 255, 0.075) inset, 0 0 8px rgba(114, 96, 153, 0.6);
    outline: 0 none;
}

</style>
