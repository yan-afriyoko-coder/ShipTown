<template>
    <div>
        <div class="row card p-2" >
            <div class="col pl-1">
                <div class="row text-left">
                    <div class="col-md-6 col-lg-5">
                        <product-info-card :product= "product"></product-info-card>
                    </div>
                    <div class="col-md-6 col-lg-7" @click="toggle">
                      <div class="table-responsive small">

                          <table class="table table-borderless small mb-0 w-100 text-right">
                              <thead>
                              <tr class="small">
                                  <th class="text-left">Location</th>
                                  <th class="text-left">Shelf</th>
                                  <th>Available</th>
                                  <th class="d-none d-md-table-cell">Reserved</th>
                                  <th class="d-none d-md-table-cell">In Stock</th>
                                  <th>Incoming</th>
<!--                                  <th><div class="d-md-none">RL</div><div class="d-none d-md-table-cell">Restock Level</div></th>-->
<!--                                  <th><div class="d-md-none">RP</div><div class="d-none d-md-table-cell">Reorder Point</div></th>-->
                                  <th class="d-none d-md-table-cell">Required</th>
                                  <th>Price</th>
                              </tr>
                              </thead>
                              <tbody>
                              <template v-for="inventory in product.inventory" >
                                  <tr class="" v-bind:class="{ 'table-active': currentUser()['warehouse'] && inventory['warehouse_code'] === currentUser()['warehouse']['code']}">
                                      <td class="text-left">{{ inventory['warehouse_code'] }}</td>
                                      <td class="text-left">{{ inventory['shelf_location'] }}</td>
                                      <td>{{ toNumberOrDash(inventory['quantity_available'])}}</td>
                                      <td class="d-none d-md-table-cell">{{ toNumberOrDash(inventory['quantity_reserved'])}}</td>
                                      <td class="d-none d-md-table-cell">{{ toNumberOrDash(inventory['quantity'])}}</td>
                                      <td>{{ toNumberOrDash(inventory['quantity_incoming']) }}</td>
                                      <td class="d-none d-md-table-cell">{{ toNumberOrDash(inventory['quantity_required']) }}</td>
                                      <td>{{ toNumberOrDash(product.prices[inventory['warehouse_code']]['price']) }}</td>
                                  </tr>
                              </template>
                              </tbody>
                          </table>
                        </div>
                    </div>
                </div>

                <div v-if="!showDetails" @click="toggle" class="row text-center text-secondary" >
                    <div class="col offset-md-6 offset-lg-5">
                        <font-awesome-icon icon="chevron-down" class="fa fa-xs"></font-awesome-icon>
                    </div>
                </div>

                <div v-else @click="toggle" class="row text-center">
                    <div class="col offset-md-6 offset-lg-5">
                        <font-awesome-icon icon="chevron-up" class="fa fa-xs text-secondary"></font-awesome-icon>
                    </div>
                </div>

                <div class="row" v-if="showDetails">
                    <div class="col offset-md-6 offset-lg-5">
                        <div class="row tabs-container mb-2">
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link p-0 pl-2 pr-2 active"  @click.prevent="currentTab = 'inventory'" data-toggle="tab" href="#">
                                        Inventory
                                    </a>
                                </li>
                                <li class="nav-item">
                                  <a class="nav-link p-0 pl-2 pr-2"  @click.prevent="currentTab = 'prices'" data-toggle="tab" href="#">
                                    Pricing
                                  </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link p-0 pl-2 pr-2"  @click.prevent="currentTab = 'aliases'" data-toggle="tab" href="#">
                                        Aliases
                                    </a>
                                </li>
                                <li class="nav-item">
                                  <a class="nav-link p-0 pl-2 pr-2"  @click.prevent="currentTab = 'activityLog'" data-toggle="tab" href="#">
                                    Activity
                                  </a>
                                </li>
                                <li class="nav-item">
                                    <a v-if="sharingAvailable()" @click.prevent="shareLink" class="nav-link p-0 pl-2 pr-2" href="#">
                                        <font-awesome-icon icon="share-alt" class="fas fa-sm"></font-awesome-icon>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <template v-if="currentTab === 'inventory'">
                            <div class="table-responsive small">
                                <table class="table table-borderless mb-0 w-100 small text-right">
                                    <thead>
                                    <tr class="small">
                                        <th class="text-left">Warehouse</th>
                                        <th>Required</th>
                                        <th>Reorder Point</th>
                                        <th>Restock Level</th>
                                        <th class="d-none d-md-table-cell">In Stock</th>
                                        <th class="d-none d-md-table-cell">Reserved</th>
                                        <th>Incoming</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <template v-for="inventory in product.inventory" >
                                        <tr class="" v-bind:class="{ 'table-active': currentUser()['warehouse'] && inventory['warehouse_code'] === currentUser()['warehouse']['code']}">
                                            <td class="text-left">{{ inventory['warehouse_code'] }}</td>
                                            <td>{{ toNumberOrDash(inventory['quantity_required']) }}</td>
                                            <td>{{ toNumberOrDash(inventory['reorder_point']) }}</td>
                                            <td>{{ toNumberOrDash(inventory['restock_level']) }}</td>
                                            <td class="d-none d-md-table-cell">{{ toNumberOrDash(inventory['quantity'])}}</td>
                                            <td class="d-none d-md-table-cell">{{ toNumberOrDash(inventory['quantity_reserved'])}}</td>
                                            <td>{{ toNumberOrDash(inventory['quantity_incoming']) }}</td>
                                        </tr>
                                    </template>
                                    </tbody>
                                </table>

                                <hr>

                                <div class="row col d-block font-weight-bold pb-1 text-uppercase small text-secondary align-content-center text-center">ORDERS</div>

                                <template>
                                    <div>
                                        {{ statusMessageOrder }}
                                    </div>
                                    <div v-if="!orders.length" class="text-center text-secondary small">
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
                                    </div>
                                </template>

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
                          <div class="table-responsive">
                            <table class="table table-borderless mb-0 w-100 small">
                                    <thead>
                                        <tr class="small">
                                            <th>Location</th>
                                            <th class="text-right">Sale Price</th>
                                            <th class="text-right">Start Date</th>
                                            <th class="text-right">End Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       <template v-for="price in product['prices']">

                                        <tr :key="price.id" v-bind:class="{ 'table-active': currentUser()['warehouse'] && price['location_id'] === currentUser()['warehouse']['code']}" >
                                            <td>{{ price['location_id'] }}</td>
                                            <td class="text-right">{{ price['sale_price'] }}</td>
                                            <td class="text-right">{{ price['sale_price_start_date'] }}</td>
                                            <td class="text-right">{{ price['sale_price_end_date'] }}</td>
                                        </tr>
                                       </template>
                                    </tbody>
                                </table>
                            </div>
                        </template>

                        <template v-if="currentTab === 'aliases'">
                            <div class="container">

                                <div v-for="alias in product.aliases" :key="alias.id">
                                    <div class="badge mb-2">{{ alias.alias }}</div>
                                </div>
                            </div>
                        </template>

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

        mounted: function () {
          this.currentTab = 'inventory';
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
                showDetails: false,
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
                this.showDetails = !this.showDetails;
                if (this.showDetails) {
                    this.loadActiveOrders();
                    this.loadCompletedOrders(10);
                    this.currentTab = 'inventory';
                }
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
.table th, .table td {;
    padding: 0.0rem;
}

.btn:active{
    background-color: rgb(94, 79, 126);
    border-color:rgb(94, 79, 126);
    box-shadow: 0 1px 1px rgba(255, 255, 255, 0.075) inset, 0 0 8px rgba(114, 96, 153, 0.6);
    outline: 0 none;
}

</style>
