<template>

  <div class="swiper-container mb-1">
    <div class="swiper-wrapper">

<!--      <div class="swiper-slide error bg-success text-right">-->
<!--        <div class="swipe-action-container swipe-action-container&#45;&#45;right">-->
<!--          <div>PICK ALL</div>-->
<!--        </div>-->
<!--      </div>-->

      <div class="swiper-slide">
          <div class="row ml-1 mr-1 card">
              <div class="col p-2 pl-2 rounded">
                  <div class="row">
                          <h5 class="text-primary">
                              <font-awesome-icon icon="copy" class="fa-xs" role="button" @click="copyToClipBoard(order['order_number'])"></font-awesome-icon>
                              <a :href="'/orders/?search=' + order['order_number']">{{ order['order_number'] }}</a>
                              <a @click="kickOrder" class="text-white">o</a>
                          </h5>
                  </div>
                <div class="row align-text-top">

                    <div class="col-5 col-md-4 col-lg-6 align-text-top">
                        <div class="small font-weight-bold">{{ order['status_code'] }}</div>
                        <div class="small">{{ order['label_template'] }} </div>
                        <template v-for="tag in order.tags">
                            <a class="badge text-uppercase" :key="tag.id" :href="'orders?has_tags=' + tag.name"> {{ tag.name }} </a>
                        </template>
                    </div>

                    <div v-if="order['order_products_totals']" class="col text-center" @click="toggleOrderDetails">
                        <div class="row ">
                            <div class="col">
                                <small> age </small>
                                <h5> {{ order['age_in_days'] }}</h5>
                            </div>
                            <div class="col">
                                <small> lines </small>
                                <h5> {{ order['order_products_totals']['count'] }} </h5>
                            </div>
                            <div class="col d-none d-md-block text-right align-text-top">
                                <div class="text-center w-100">
                                    <small>total</small>
                                </div>
                                <span class="pr-0 mr-2 h5">{{ Math.floor(order['order_products_totals']['total_price'] + order['total_shipping']) }}<span class="" style="font-size: 8pt"><template v-if="(order['order_products_totals']['total_price'] + order['total_shipping']) % 1 === 0"> .00</template><template v-if="(order['order_products_totals']['total_price'] + order['total_shipping']) % 1 > 0"> .{{ Math.floor((order['order_products_totals']['total_price'] + order['total_shipping'])% 1 * 100) }} </template></span></span>
                            </div>
                            <div class="col d-none d-sm-block">
                                <small> ordered </small>
                                <h5>{{ toNumberOrDash(order['order_products_totals']['quantity_ordered']) }}</h5>
                            </div>
                            <div class="col bg-warning" v-if="Number(order['order_products_totals']['quantity_split']) > 0">
                                <small> split </small>
                                <h5>{{ toNumberOrDash(order['order_products_totals']['quantity_split']) }}</h5>
                            </div>
                            <div class="col">
                                <small> picked </small>
                                <h5>{{ toNumberOrDash(order['order_products_totals']['quantity_picked']) }}</h5>
                            </div>
                            <div class="col bg-warning" v-if="Number(order['order_products_totals']['quantity_skipped_picking']) > 0">
                                <small> skipped </small>
                                <h5>{{ toNumberOrDash(order['order_products_totals']['quantity_skipped_picking']) }}</h5>
                            </div>
                            <div class="col d-none d-md-block">
                                <small> shipped </small>
                                <h5>{{ toNumberOrDash(order['order_products_totals']['quantity_shipped'])  }}</h5>
                            </div>
                            <div class="col">
                                <small> to ship </small>
                                <h5>{{ toNumberOrDash(order['order_products_totals']['quantity_to_ship'])  }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 small" v-if="order['order_comments'].length > 0 && orderDetailsVisible === false">
                        <b>{{ order['order_comments'][0]['user'] ? order['order_comments'][0]['user']['name'] : 'AutoPilot' }}:</b> {{ order['order_comments'][0]['comment'] }}
                    </div>

                </div>


                <div class="row text-center text-secondary" v-if="!orderDetailsVisible" @click="toggleOrderDetails">
                    <div class="col">
                        <font-awesome-icon icon="chevron-down" class="fa fa-xs"></font-awesome-icon>
                    </div>
                </div>

                <div v-if="orderDetailsVisible">

                    <div class="row text-center" @click="toggleOrderDetails">
                        <div class="col">
                            <font-awesome-icon icon="chevron-up" class="fa fa-xs text-secondary"></font-awesome-icon>
                        </div>
                    </div>

                    <div class="row mb-2 mt-1">
                        <input ref="newCommentInput" v-model="input_comment" class="form-control" placeholder="Add comment here" @keypress.enter="addComment"/>
                    </div>

                    <template v-for="order_comment in order['order_comments']">
                        <div class="row mb-2">
                            <div class="col">
                                <b>{{ order_comment['user'] ? order_comment['user']['name'] : 'AutoPilot' }}:</b> {{ order_comment['comment'] }}
                            </div>
                        </div>
                    </template>

                    <div class="row tabs-container mb-2">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active p-0 pl-2 pr-2" data-toggle="tab" href="#" @click.prevent="currentTab = 'productsOrdered'" >
                                    Products
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link p-0 pl-2 pr-2" data-toggle="tab" href="#" @click.prevent="currentTab = 'shippingAddress'" >
                                    Address
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link p-0 pl-2 pr-2" data-toggle="tab" href="#" @click.prevent="currentTab = 'orderDetails'" >
                                    Details
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link p-0 pl-2 pr-2" data-toggle="tab" href="#" @click.prevent="currentTab = 'orderActivities'" >
                                    Activity
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link p-0 pl-2 pr-2" target="_blank" :href="'/order/packsheet/' + order['id'] + '?hide_nav_bar=true'">
                                    Packsheet
                                </a>
                            </li>
                            <li class="nav-item">
                                <a v-if="sharingAvailable()" class="nav-link p-0 pl-2 pr-2" @click.prevent="shareLink" href="#">
                                    <font-awesome-icon icon="share-alt" class="fas fa-sm"></font-awesome-icon>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="container" v-if="currentTab === 'productsOrdered'">
                        <template v-for="order_product in order_products">

                            <div class="row text-left mb-2">
                                <div class="col-md-4 col-lg-6">
                                    <small>{{ order_product['name_ordered'] }} &nbsp;</small>
                                    <div class="small"><a target="_blank" :href="getProductLink(order_product)">{{ order_product['sku_ordered'] }}</a>&nbsp;</div>
                                </div>
                                <div class="col">
                                    <div class="row text-center">
                                        <div class="col">
                                            <small> ordered </small>
                                            <h4>{{ toNumberOrDash(order_product['quantity_ordered']) }}</h4>
                                        </div>
                                        <div class="col bg-warning" v-if="Number(order_product['quantity_split']) > 0">
                                            <small> split </small>
                                            <h4>{{ toNumberOrDash(order_product['quantity_split']) }}</h4>
                                        </div>
                                        <div class="col d-none d-md-block text-right">
                                            <div class="text-center w-100">
                                                <small>unit price</small>
                                            </div>
                                            <span class="pr-0 mr-2 h4">{{ Math.floor(order_product['price']) }}<span class="ml-0 pl-0" style="font-size: 8pt"><template v-if="order_product['price'] % 1 === 0"> .00</template><template v-if="order_product['price'] % 1 > 0"> .{{ Math.floor(order_product['price'] % 1 * 100) }} </template></span></span>
                                        </div>
                                        <div class="col">
                                            <small> picked </small>
                                            <h4>{{ toNumberOrDash(order_product['quantity_picked']) }}</h4>
                                        </div>
                                        <div class="col bg-warning" v-if="Number(order_product['quantity_skipped_picking']) > 0">
                                            <small> skipped </small>
                                            <h4>{{ toNumberOrDash(order_product['quantity_skipped_picking']) }}</h4>
                                        </div>
                                        <div class="col d-none d-sm-block">
                                            <small> shipped </small>
                                            <h4>{{ toNumberOrDash(order_product['quantity_shipped'])  }}</h4>
                                        </div>
                                        <div class="col">
                                            <small> to ship </small>
                                            <h4>{{ toNumberOrDash(order_product['quantity_to_ship'])  }}</h4>
                                        </div>
                                        <div class="col" v-bind:class="{ 'bg-warning': ifHasEnoughStock(order_product) }">
                                            <small> inventory </small>
                                            <h4>{{ toNumberOrDash(getProductQuantity(order_product)) }}</h4>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <hr>
                        </template>
                    </div>

                    <div class="container" v-if="currentTab === 'shippingAddress'">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table-borderless table-hover border-0">
                                      <tr><td class="font-weight-bold">email:</td><td><a :href="'mailto:' + order['shipping_address']['email'] + '?subject=Order #' + order['order_number']">{{ order['shipping_address']['email'] }} </a></td></tr>
                                      <tr class="pb-2"><td class="font-weight-bold">phone:</td><td><a :href="'tel:' + order['shipping_address']['phone']">{{ order['shipping_address']['phone'] }}</a> </td></tr>
                                      <tr><td class="font-weight-bold"></td><td> &nbsp;</td></tr>
                                      <tr><td class="font-weight-bold">first_name:</td><td> {{ order['shipping_address']['first_name'] }}</td></tr>
                                      <tr><td class="font-weight-bold">last_name:</td><td> {{ order['shipping_address']['last_name'] }}</td></tr>
                                      <tr><td class="font-weight-bold">company:</td><td> {{ order['shipping_address']['company'] }}</td></tr>
                                      <tr><td class="font-weight-bold">address1:</td><td> {{ order['shipping_address']['address1'] }}</td></tr>
                                      <tr><td class="font-weight-bold">address2:</td><td> {{ order['shipping_address']['address2'] }}</td></tr>
                                      <tr><td class="font-weight-bold">postcode:</td> <td>{{ order['shipping_address']['postcode'] }}</td></tr>
                                      <tr><td class="font-weight-bold">city:</td><td> {{ order['shipping_address']['city'] }}</td></tr>
                                      <tr><td class="font-weight-bold">country_code:</td><td> {{ order['shipping_address']['country_code'] }}</td></tr>
                                      <tr><td class="font-weight-bold">country_name:</td><td> {{ order['shipping_address']['country_name'] }}</td></tr>
                                      <tr><td class="font-weight-bold">fax:</td><td> {{ order['shipping_address']['fax'] }}</td></tr>
                                      <tr><td class="font-weight-bold">region:</td> <td>{{ order['shipping_address']['region'] }}</td></tr>
                                      <tr><td class="font-weight-bold">state_code:</td> <td>{{ order['shipping_address']['state_code'] }}</td></tr>
                                      <tr><td class="font-weight-bold">state_name:</td> <td>{{ order['shipping_address']['state_name'] }}</td></tr>
                                      <tr><td class="font-weight-bold">website:</td> <td>{{ order['shipping_address']['website'] }}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="container" v-if="currentTab === 'orderDetails'">
                        <div class="row">
                            <div class="col-md-6">
                              <table class="table-borderless table-hover border-0">
                                <tr>
                                  <td> placed at: </td>
                                  <td><b> {{ order['order_placed_at'] | moment('MMM DD H:mm') }} </b> </td>
                                </tr>
                                <tr>
                                  <td> shipping_method: </td><td><b> {{ order['shipping_method_code'] }} </b> </td>
                                </tr>
                                <tr>
                                  <td> label_template: </td><td><b> {{ order['label_template'] }} </b> </td>
                                </tr>
                                <tr>
                                  <td> total_products: </td><td><b> {{ order['order_products_totals']['total_price'] }} </b> </td>
                                </tr>
                                <tr>
                                  <td> total_shipping: </td><td><b> {{ toNumberOrDash(order['total_shipping']) }} </b> </td>
                                </tr>
                                <tr>
                                  <td> total: </td><td><b> {{ order['order_products_totals']['total_price'] + order['total_shipping'] }} </b> </td>
                                </tr>
                                <tr>
                                  <td> paid: </td><td><b> {{ order['total_paid'] }} </b> </td>
                                </tr>
                                <tr>
                                  <td> picked at: </td><td><b> {{ order['picked_at'] | moment('MMM DD H:mm') }} </b> </td>
                                </tr>
                                <tr>
                                  <td> packed at: </td><td><b> {{ (order['packed_at']) | moment('MMM DD H:mm') }} </b> </td>
                                </tr>
                                <tr>
                                  <td> packed by: </td><td><b> {{ order['packer'] ? order['packer']['name'] : '&nbsp' }} </b> </td>
                                </tr>
                                <tr>
                                  <td> closed at: </td><td><b> {{ (order['order_closed_at']) | moment('MMM DD H:mm') }} </b> </td>
                                </tr>
                                <tr>
                                  <td>.</td><td></td>
                                </tr>
                                <tr>
                                  <td colspan="2"> Shipping Numbers: </td>
                                </tr>
                                <template v-for="shipment in order_shipments">
                                  <tr>
                                    <td colspan="2">
                                      <b>
                                        {{ shipment['created_at'] | moment('MMM DD')  }} <small>@</small> {{ shipment['created_at'] | moment('H:mm')  }}:
                                      </b>
                                      <a :href="shipment['tracking_url']" target="_blank">
                                        {{ shipment['shipping_number'] }}
                                      </a>
                                        by {{ shipment['user'] ? shipment['user']['name'] : ''}}
                                        <a :href="shippingContentUrl(shipment)" target="_blank" style="color: white">
                                           o
                                        </a>
                                    </td>
                                  </tr>
                                </template>
                              </table>
                                <div>.</div>
                            </div>
                        </div>
                    </div>

                    <div class="container" v-if="currentTab === 'orderActivities'">
                        <template v-for="activity in order_activities">
                            <div class="d-flex flex-column flex-md-row align-middle">
                                <div class="d-none d-md-block align-middle" :title="activity['created_at'] | moment('YYYY-MM-DD H:mm:ss') ">
                                    {{ activity['created_at'] | moment('MMM DD')  }} <small>@</small> {{ activity['created_at'] | moment('H:mm')  }}:
                                </div>
                                <div class="small flex-row d-block d-md-none align-middle">
                                    {{ activity['created_at'] | moment('MMM DD')  }}
                                    {{ activity['created_at'] | moment('H:mm')  }}:
                                </div>
                                <div class="pl-sm-0 pl-md-1">
                                    <b>
                                        {{ activity['causer'] === null ? 'AutoPilot' : activity['causer']['name'] }}:
                                    </b>
                                    {{ activity['description'] }} {{ activity['properties']['attributes'] ? activity['properties']['attributes'] : activity['properties'] }}
                                </div>
                            </div>
                        </template>
                    </div>

                </div>
              </div>
            </div>
          <div id="spacer-bottom" class="row mb-4 mt-4" v-if="orderDetailsVisible"></div>
      </div>
    </div>

<!--    <div class="swiper-slide bg-warning">-->
<!--    <div class="swipe-action-container swipe-action-container&#45;&#45;left text-black-50 font-weight-bold">-->
<!--      <div>PARTIAL PICK</div>-->
<!--    </div>-->
<!--    </div>-->

</div>

</template>

<script>
    import api from "../../mixins/api";
    import helpers from "../../mixins/helpers";
    import url from "../../mixins/url";
    import Vue from "vue";

    export default {
        mixins: [api, helpers, url],
        name: "OrderCard",

        props: {
            order: Object,
            expanded: false,
        },


        data: function () {
            return {
                input_comment: '',
                orderDetailsVisible: false,

                currentTab: 'productsOrdered',

                order_products: [],
                order_comments: null,
                order_activities: null,
                order_shipments: null,
            }
        },

        mounted() {
            // we pre creating array of empty products so UI will display empty row for each orderProduct
            // its simply more pleasant to eye and card doesnt "jump"
            for (let i = 0; i < this.order['product_line_count']; i++)
                this.order_products.unshift([]);
        },

        created: function () {
            if (this.expanded) {
                this.toggleOrderDetails();
            }
        },

        watch: {
            expanded() {
                if (this.expanded) {
                    this.toggleOrderDetails();
                }
            },
        },

        methods: {
            formatPrice: function (price) {
                if (price === 0) {
                    return '-';
                }

                return price.toFixed();
            },

            shippingContentUrl: function(shipment) {
                return '/shipping-labels/' + shipment['id'];
            },

            kickOrder: function () {
                this.apiPostOrderCheckRequest({'order_id': this.order['id']});
            },

            sharingAvailable() {
                return navigator.share;
            },

            shareLink() {
                navigator.share({
                    url: '/orders?search=' + this.order['order_number'],
                    title: document.title
                });
            },

            toggleOrderDetails() {
                if (this.orderDetailsVisible) {
                    this.orderDetailsVisible = false;
                    return;
                }

                this.loadOrderProducts()
                this.loadOrderActivities();
                this.loadOrderShipments();

                this.orderDetailsVisible = true;
            },

            loadOrderProducts() {
                let params = {
                    'filter[warehouse_id]': this.getUrlParameter('warehouse_id'),
                    'filter[order_id]': this.order['id'],
                    'include': 'product',
                    'per_page': '999',
                };

                this.apiGetOrderProducts(params)
                    .then(({data}) => {
                        this.order_products = data.data;
                    })

                return this;
            },

            loadOrderComments() {
                if (this.order_comments) {
                    return this;
                }

                let params = {
                    'filter[order_id]': this.order['id'],
                    'include': 'user',
                    'sort': '-id',
                    'per_page': '999',
                };

                this.apiGetOrderComments(params)
                    .then(({data}) => {
                        this.order_comments = data.data;
                    })

                return this;
            },

            loadOrderActivities() {
                if (this.order_activities) {
                    return this;
                }

                let params = {
                    'filter[subject_id]': this.order['id'],
                    'filter[subject_type]': 'App\\Models\\Order',
                    'include': 'causer',
                    'sort': '-id',
                    'per_page': '999',
                };

                this.apiGetActivityLog(params)
                    .then(({data}) => {
                        this.order_activities = data.data;
                    })

                return this;
            },

            loadOrderShipments() {
                if (this.order_shipments) {
                    return this;
                }

                let params = {
                    'filter[order_id]': this.order['id'],
                    'include': 'user',
                    'sort': '-id',
                    'per_page': '999',
                };

                this.apiGetOrderShipments(params)
                    .then(({data}) => {
                        this.order_shipments = data.data;
                    })

                return this;
            },

            hasSkippedPick(orderProduct) {
                return Number(orderProduct['quantity_skipped_picking']) > 0;
            },

            addComment() {
                let data = {
                    "order_id": this.order['id'],
                    "comment": this.input_comment
                };

                this.apiPostOrderComment(data)
                    .then(({data}) => {
                        // quick hack to immediately display comment
                        this.order['order_comments'].unshift(data['data'][0])
                        this.loadOrderComments();
                        this.input_comment = '';
                    })
            },

            getProductLink(orderProduct) {
                const searchTerm = orderProduct['product'] ? orderProduct['product']['sku'] : orderProduct['sku_ordered'];
                return '/products?search=' + searchTerm + '&hide_nav_bar=true';
            },

            getProductQuantity(orderProduct) {
                if(this.getUrlParameter('warehouse_id')) {
                    return orderProduct['inventory_source_quantity']
                }
                return orderProduct['product'] ? Number(orderProduct['product']['quantity']) : 0;
            },

            ifHasEnoughStock(orderProduct) {
                return this.getProductQuantity(orderProduct) < Number(orderProduct['quantity_to_ship']);
            }
        },
    }
</script>

<style scoped>

    .header-row > div, .col {
      /*border: 1px solid #76777838;*/
    }

    .col {
        background-color: #ffffff;
        /*border: 1px solid #76777838;*/
    }

    .nav-item {
        margin-right: unset;
    }
</style>
