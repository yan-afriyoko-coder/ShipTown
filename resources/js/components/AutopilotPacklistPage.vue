<template>
    <div>
        <div v-if="! order_number" class="row text-center mt-3" >
            <button type="button"  class="btn-info" @click.prevent="loadNextOrder">
                Start AutoPilot Packing
            </button>
        </div>

        <autopilot-packsheet-page v-if="order_number" :order_number="order_number" @orderCompleted="loadNextOrder"></autopilot-packsheet-page>
    </div>
</template>

<script>
    import url from "../mixins/url";
    import api from "../mixins/api";
    import beep from "../mixins/beep";
    import Vue from "vue";
    import helpers from "../mixins/helpers";

    export default {
        mixins: [api, beep, url, helpers],

        data: function() {
            return {
                order_number: null,
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

                let params = {
                    'filter[status]': this.getUrlParameter('status','picking'),
                    'filter[inventory_source_warehouse_id]': this.getUrlParameter('inventory_source_warehouse_id'),
                    'sort': this.getUrlParameter('sort', 'order_placed_at'),
                };

                this.apiGetPacklistOrder(params)
                    .then(({data}) => {
                        this.order_number = data.data['order_number'];
                    })
                    .catch((error) => {
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
