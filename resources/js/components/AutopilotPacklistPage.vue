<template>
    <div>
        <div v-if="!order_number" class="row text-center mt-3" >
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

    export default {
        mixins: [api, beep, url],

        data: function() {
            return {
                order_number: null,
            };
        },

        watch: {
            user: {
                handler() {
                    if(Vue.prototype.$currentUser['location_id']) {
                        this.setUrlParameter('inventory_source_location_id', Vue.prototype.$currentUser['location_id']);
                    }
                }
            },
        },

        methods: {
            loadNextOrder() {
                let params = {
                    'filter[status]': this.getUrlParameter('status','picking'),
                    'filter[inventory_source_location_id]': this.getUrlParameter('inventory_source_location_id'),
                    'sort': this.getUrlParameter('sort', 'order_placed_at'),
                };

                this.apiGetPacklistOrder(params)
                    .then(({data}) => {
                        this.order_number = data.data['order_number'];
                    })
                    .catch((error) => {
                        let msg = 'Error occurred loading order';
                        if (error.response.status === 404) {
                            msg = "No orders available with specified filters"
                        }
                        this.$snotify.error(msg);
                        this.errorBeep();
                    })
            },
        },
    }
</script>


<style lang="scss">

</style>
