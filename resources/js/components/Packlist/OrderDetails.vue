<template>
    <div v-if="order">
        <div class="card">
            <div class="row rounded p-2">
                <div class="col-12 col-lg-6">
                    <h5 class="text-primary">
                        <font-awesome-icon icon="copy" class="fa-xs" role="button" @click="copyToClipBoard(order['order_number'])"></font-awesome-icon>
                        <a :href="'/orders/?search=' + order['order_number']">{{ order['order_number'] }}</a>
                    </h5>

                    <div class="small font-weight-bold">{{ order['status_code'] }}</div>
                    <div class="small font-weight-bold">{{ formatDateTime(order['created_at']) }}</div>
                    <div class="small">{{ order['label_template'] }}</div>
                </div>
                <div class="col text-right small">
                    <number-card :number="order['age_in_days']" label="age"/>
                    <div class="d-none d-md-inline-block">
                        <number-card :number="order['order_products_totals']['total_price']" label="total" ></number-card>
                    </div>
                    <number-card :number="order['order_products_totals']['count']" label="lines"/>
                    <number-card :number="order['order_products_totals']['quantity_ordered']" label="ordered"/>
                    <div class="d-none d-md-inline-block">
                        <number-card :number="order['order_products_totals']['quantity_picked']" label="picked" ></number-card>
                        <number-card :number="order['order_products_totals']['quantity_shipped']" label="shipped" ></number-card>
                    </div>
                    <number-card :number="order['order_products_totals']['quantity_to_ship']" label="to ship"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from "moment";
    import helpers from "../../mixins/helpers";

    export default {
        name: "OrderDetails",
        mixins: [helpers],

        props: {
            order: Object,
        },

        methods: {
            formatDateMMMDD: (value) => {
                return moment(String(value)).format('MMM DD');
            },
        }
    }
</script>

<style scoped>

</style>
