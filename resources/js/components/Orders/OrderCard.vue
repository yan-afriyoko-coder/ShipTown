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
          <div class="col p-2 pl-3">

            <div class="row text-left">
              <div class="col-6">
                <div class="text-secondary h4">#{{ order['order_number'] }}</div>
              </div>
              <div class="col-6 text-right">
                  <div class=""><a target="_blank" :href="'/order/packsheet?order_number=' + order['order_number'] ">OPEN ORDER</a></div>
              </div>
            </div>

            <div class="row" @click="showHideProducts">
              <div class="col-6">
                <div class="text-secondary h6">date:</div>
                <div class="text-secondary h6 font-weight-bold">{{ order['order_placed_at'] | moment('MM/DD H:mm') }}</div>
                <div class="text-secondary h6">picked: <span class="font-weight-bold"> {{ order['picked_at'] | moment('MM/DD H:mm') }} </span></div>
                <div class="text-secondary h6">packed: <span class="font-weight-bold"> {{ order['packed_at'] | moment('MM/DD H:mm') }} </span></div>
                <div class="text-secondary h6">total: <span class="font-weight-bold"> {{ order['total'] }} </span></div>
                <div class="text-secondary h6">paid: <span class="font-weight-bold"> {{ order['total_paid'] }} </span></div>
              </div>
              <div class="col-6">
                <div class="text-secondary h6">status:</div>
                <div class="text-secondary h6 font-weight-bold">{{ order['status_code'] }}</div>
                <div class="text-secondary h6"><span class="font-weight-bold"> &nbsp </span></div>
                <div class="text-secondary h6"><span class="font-weight-bold"> {{ order['packer'] ? order['packer']['name'] : '&nbsp' }} </span></div>
                <div class="text-secondary h6">lines:<span class="font-weight-bold"> {{ order['product_line_count'] }} </span></div>
                <div class="text-secondary h6">quantity: <span class="font-weight-bold"> {{ order['total_quantity_ordered'] }} </span></div>
              </div>
            </div>

            <div class="row" @click="showHideProducts" v-if="order['order_comments'].length > 0 ">
              <div class="col">
                <b>{{ order['order_comments'][0]['user']['name'] }}: </b>{{ order['order_comments'][0]['comment'] }}
              </div>
            </div>

            <div class="row" @click="showHideProducts">
              <div class="col-11">
                  <template v-for="shipment in order['order_shipments']">
                      <div class="text-secondary h6"><span class="font-weight-bold">{{ shipment['shipping_number'] }}</span></div>
                  </template>
              </div>
              <div class="col-1 text-center text-primary h1">
                  +
              </div>
            </div>

            <div v-if="showOrderDetails">

                <div class="row mb-2">
                    <input v-model="input_comment" class="form-control" placeholder="Add comment here" @keypress.enter="addComment"/>
                </div>

                <template v-for="order_comment in order['order_comments']">
                    <div class="row mb-2">
                        <div class="col">
                            <b>{{ order_comment['user']['name'] }}: </b>{{ order_comment['comment'] }}
                        </div>
                    </div>
                </template>

              <template v-for="order_product in order['order_products']">

                  <hr>
                    <div class="row text-left mb-2">
                        <div class="col-lg-6">
                            <small>{{ order_product['name_ordered'] }}</small>
                            <div class="small"><a target="_blank" :href="getProductLink(order_product)">{{ order_product['sku_ordered'] }}</a></div>
                        </div>

                        <div class="col-lg-6">
                            <div class="row text-center">
                                <div class="col">
                                    <small> ordered </small>
                                    <h3>{{ Math.ceil(order_product['quantity_ordered']) }}</h3>
                                </div>
                                <div class="col">
                                    <small> picked </small>
                                    <h3>{{  }}</h3>
                                </div>
                                <div class="col" v-bind:class="{ 'bg-warning': Number(order_product['quantity_skipped_picking']) > 0 }">
                                    <small> skipped </small>
                                    <h3>{{ dashIfZero(Number(order_product['quantity_skipped_picking'])) }}</h3>
                                </div>
                                <div class="col d-none d-sm-block">
                                    <small> shipped </small>
                                    <h3>{{ dashIfZero(Number(order_product['quantity_shipped']))  }}</h3>
                                </div>
                                <div class="col">
                                    <small> to ship </small>
                                    <h3>{{ dashIfZero(Number(order_product['quantity_to_ship']))  }}</h3>
                                </div>
                                <div class="col" v-bind:class="{ 'bg-warning': ifHasEnoughStock(order_product) }">
                                    <small> inventory </small>
                                    <h3>{{ dashIfZero(getProductQuantity(order_product)) }}</h3>
                                </div>
                            </div>
                        </div>

                    </div>

              </template>

                <hr>

                <div class="row">
                    <div class="col-12">
                        <template v-for="activity in order['activities']">
                            <div class="text-secondary h6">
                                {{ activity['created_at'] | moment('MMM DD @ H:mm')  }} <b> {{ activity['causer'] === null ? 'AutoPilot' : activity['causer']['name'] }}</b> {{ activity['description'] }} {{ activity['changes'] }}
                            </div>
                        </template>
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
    export default {
        name: "OrderCard",

        props: {
            order: Object,
        },

        data: function () {
            return {
                input_comment: '',
                showOrderDetails: false,
            }
        },

        methods: {
            hasSkippedPick(orderProduct) {
                return Number(orderProduct['quantity_skipped_picking']) > 0;
            },
            addComment() {
                axios.post('/api/order/comments', {
                        "order_id": this.order['id'],
                        "comment": this.input_comment
                    })
                    .then(({data}) => {
                        this.order['order_comments'].unshift(data['data'][0]);
                        this.input_comment = '';
                    })
            },
            dashIfZero(value) {
                return value === 0 ? '-' : value;
            },
            showHideProducts() {
              this.showOrderDetails = ! this.showOrderDetails;
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
