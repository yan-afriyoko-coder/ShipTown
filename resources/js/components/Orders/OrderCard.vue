<template>

<!--  <div :id="pickCardId" class="swiper-container mb-3">-->
  <div class="swiper-container mb-1">
    <div class="swiper-wrapper">

<!--      <div class="swiper-slide error bg-success text-right">-->
<!--        <div class="swipe-action-container swipe-action-container&#45;&#45;right">-->
<!--          <div>PICK ALL</div>-->
<!--        </div>-->
<!--      </div>-->

      <div class="swiper-slide">
        <div class="row ml-1 mr-1 card" >
          <div class="col p-2 pl-3 rounded">

<!--            <div class="row text-left">-->
<!--              <div class="col-6">-->
<!--                <div class="text-secondary h4">#{{ order['order_number'] }}</div>-->
<!--              </div>-->
<!--              <div class="col-6 text-right">-->
<!--                  <div class=""><a target="_blank" :href="'/order/packsheet?order_number=' + order['order_number'] ">OPEN ORDER</a></div>-->
<!--              </div>-->
<!--            </div>-->

            <div class="row" @click="toggleOrderDetails">
                <div class="col-5 col-md-3">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <h5 class="text-primary">#{{ order['order_number'] }} </h5>
                            <div class="small"> <b> {{ order['status_code'] }} </b> </div>
                        </div>
                    </div>
                </div>
                <div class="col text-center">
                    <div class="row">
                        <div class="col">
                            <small> age </small>
                            <h5>4</h5>
                        </div>
                        <div class="col">
                            <small> products </small>
                            <h5> {{ order['product_line_count'] }} </h5>
                        </div>
                        <div class="col">
                            <small> quantity </small>
                            <h5> {{ order['total_quantity_ordered'] }} </h5>
                        </div>
                        <div class="col d-none d-sm-block">
                            <small> total </small>
                            <h5>{{ order['total'] }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col align-middle" style="max-width: 20px;">
                    <h2 class="text-primary text-center h-100 align-middle"> + </h2>
                </div>

                <div class="col-12" v-if="order['order_comments'].length > 0 && orderDetailsVisible === false">
                    <b>{{ order['order_comments'][0]['user']['name'] }}: </b>{{ order['order_comments'][0]['comment'] }}
                </div>

            </div>


<!--            <div class="row" @click="showHideProducts">-->
<!--              <div class="col-11">-->
<!--                  <template v-for="shipment in order['order_shipments']">-->
<!--                      <div class="text-secondary h6"><span class="font-weight-bold">{{ shipment['shipping_number'] }}</span></div>-->
<!--                  </template>-->
<!--              </div>-->
<!--              <div class="col-1 text-center text-primary h1">-->
<!--                  +-->
<!--              </div>-->
<!--            </div>-->

            <div v-if="orderDetailsVisible">

                <div class="row mb-2 mt-1">
                    <input ref="newCommentInput" v-model="input_comment" class="form-control" placeholder="Add comment here" @keypress.enter="addComment"/>
                </div>

<!--                <div class="row small">-->
<!--                    <div class="col-4">-->
<!--                        <div> date: <b> {{ order['order_placed_at'] | moment('MM/DD H:mm') }} </b> </div>-->
<!--                        <div> paid: <b> {{ order['total_paid'] }} </b> </div>-->
<!--                    </div>-->
<!--&lt;!&ndash;                    <div class="col">&ndash;&gt;-->
<!--&lt;!&ndash;                        <div> picked at: <b> {{ order['picked_at'] | moment('MM/DD H:mm') }} </b> </div>&ndash;&gt;-->
<!--&lt;!&ndash;                        <div> packed at: <b> {{ order['packed_at'] | moment('MM/DD H:mm') }} </b> </div>&ndash;&gt;-->
<!--&lt;!&ndash;                        <div> packed by: <b> {{ order['packer'] ? order['packer']['name'] : '&nbsp' }} </b> </div>&ndash;&gt;-->
<!--&lt;!&ndash;                    </div>&ndash;&gt;-->
<!--                </div>-->

                <template v-for="order_comment in order_comments">
                    <div class="row mb-2">
                        <div class="col">
                            <b>{{ order_comment['user']['name'] }}: </b>{{ order_comment['comment'] }}
                        </div>
                    </div>
                </template>

                <div class="row tabs-container mb-2">
                    <ul class="nav nav-tabs">
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab-general" @click.prevent="currentTab = 'productsOrdered'" >Product Ordered</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#" @click.prevent="currentTab = 'orderActivities'" >Activity Log</a></li>
                    </ul>
                    <div class="tab-content">

                        <!--                            @include('products.sections.modal.tabs.general')-->

                        <!--                            @include('products.sections.modal.tabs.stock')-->

                        <!--                            @include('products.sections.modal.tabs.onOrder')-->

                    </div>
                </div>



                <div class="container" v-if="currentTab === 'productsOrdered'">
                    <template v-for="order_product in order_products">

                        <div class="row text-left mb-2">
                            <div class="col-lg-6">
                                <small>{{ order_product['name_ordered'] }}</small>
                                <div class="small"><a target="_blank" :href="getProductLink(order_product)">{{ order_product['sku_ordered'] }}</a></div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row text-center">
                                    <div class="col">
                                        <small> ordered </small>
                                        <h3>{{ toNumberOrDash(order_product['quantity_ordered']) }}</h3>
                                    </div>
                                    <div class="col">
                                        <small> picked </small>
                                        <h3>{{ toNumberOrDash(order_product['quantity_picked']) }}</h3>
                                    </div>
                                    <div class="col" v-bind:class="{ 'bg-warning': Number(order_product['quantity_skipped_picking']) > 0 }">
                                        <small> skipped </small>
                                        <h3>{{ toNumberOrDash(order_product['quantity_skipped_picking']) }}</h3>
                                    </div>
                                    <div class="col d-none d-sm-block">
                                        <small> shipped </small>
                                        <h3>{{ toNumberOrDash(order_product['quantity_shipped'])  }}</h3>
                                    </div>
                                    <div class="col">
                                        <small> to ship </small>
                                        <h3>{{ toNumberOrDash(order_product['quantity_to_ship'])  }}</h3>
                                    </div>
                                    <div class="col" v-bind:class="{ 'bg-warning': ifHasEnoughStock(order_product) }">
                                        <small> inventory </small>
                                        <h3>{{ toNumberOrDash(getProductQuantity(order_product)) }}</h3>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <hr>
                    </template>
                </div>

                <div class="container" v-if="currentTab === 'orderActivities'">
                    <div class="row">
                        <div class="col-12">
                            <template v-for="activity in order_activities">
                                <div class="text-secondary h6">
                                    {{ activity['created_at'] | moment('MMM DD @ H:mm')  }}
                                    <b>
                                        {{ activity['causer'] === null ? 'AutoPilot' : activity['causer']['name'] }}
                                    </b>
                                    {{ activity['description'] }} {{ activity['changes'] }}
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

            </div>
          </div>
        </div>
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

    export default {
        name: "OrderCard",

        props: {
            order: Object,
        },

        mixins: [api, helpers],

        data: function () {
            return {
                input_comment: '',
                orderDetailsVisible: false,

                currentTab: 'productsOrdered',

                order_products: null,
                order_comments: null,
                order_activities: null,
            }
        },

        methods: {
            loadOrderProducts() {
                if (this.order_products) {
                    return this;
                }

                let params = {
                    'filter[order_id]': this.order['id']
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
                    'sort': '-created_at',
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
                    'include': 'causer',
                    'sort': 'created_at',
                };

                this.apiGetOrderActivities(params)
                    .then(({data}) => {
                        this.order_activities = data.data;
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

            toggleOrderDetails() {
                if (this.orderDetailsVisible) {
                    this.orderDetailsVisible = false;
                    return;
                }

                this.orderDetailsVisible = true;
                this.loadOrderComments();
                this.loadOrderProducts();
                this.loadOrderActivities();
            },

            getProductLink(orderProduct) {
                const searchTerm = orderProduct['product'] ? orderProduct['product']['sku'] : orderProduct['sku_ordered'];
                return '/products?search=' + searchTerm;
            },
            getProductQuantity(orderProduct) {
                return orderProduct['product'] ? Number(orderProduct['product']['quantity']) : -1;
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

    .widget {
        width: 20%;
    }
</style>
