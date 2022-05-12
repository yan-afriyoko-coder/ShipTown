<template>
    <div>
        <div v-if="! order_number" class="row text-center mt-3" >
            <div class="col">
                <button type="button"  class="btn btn-primary" @click.prevent="loadNextOrder">
                    Start AutoPilot Packing
                </button>
            </div>
        </div>

        <template v-for="order in orders">
            <order-packsheet-page :key="'order_id_' + order.id" :order_id="order.id" :order_number="order.order_number" @orderCompleted="loadNextOrder"></order-packsheet-page>
        </template>
    </div>
</template>

<script>
    import url from "../mixins/url";
    import api from "../mixins/api";
    import beep from "../mixins/beep";
    import Vue from "vue";
    import helpers from "../mixins/helpers";
    import loadingOverlay from "../mixins/loading-overlay";

    export default {
        mixins: [loadingOverlay, api, beep, url, helpers],

        data: function() {
            return {
                orders: [],
                order_number: null,
                order_id: null,
            };
        },

        mounted() {
            if(Vue.prototype.$currentUser['warehouse_id']) {
                this.setUrlParameter('inventory_source_warehouse_id', Vue.prototype.$currentUser['warehouse_id']);
            }
        },

        methods: {
            loadNextOrder() {
                if(! Vue.prototype.$currentUser['warehouse_id']) {
                    this.notifyError('User does not have warehouse assigned! Please assign in Settings->User');
                    return;
                }

                this.showLoading();

                this.orders = [];

                let params = {
                    'filter[status]': this.getUrlParameter('status','picking'),
                    'filter[inventory_source_warehouse_id]': this.getUrlParameter('inventory_source_warehouse_id'),
                    'sort': this.getUrlParameter('sort', 'order_placed_at'),
                };

                this.apiGetPacklistOrder(params)
                    .then(({data}) => {
                        // we use array here so we can use v-for component
                        // and auto destroy when loading next order
                        this.orders = [data.data];
                        this.order_number = data.data['order_number'];
                        this.order_id = data.data['id'];
                        this.hideLoading();
                    })
                    .catch((error) => {
                        this.hideLoading();

                        let msg = error.response.data.errors;

                        if (error.response.status === 404) {
                            msg = "No orders available with specified filters"
                        }
                        this.notifyError(msg);
                        this.errorBeep();
                    })
            },
        },
    }
</script>


<style lang="scss">

</style>
