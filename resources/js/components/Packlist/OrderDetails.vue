<template>
    <div class="col" v-if="order">
        <div class="row">
            <div class="col-3">
              <label>Order #: </label>
              <br>
              <b>{{ order['order_number'] }}</b>
            </div>
            <div class="col-3">
              <label>Date:</label>
              <br>
              <b>{{ formatDateMMMDD(order['order_placed_at']) }}</b>
            </div>
            <div class="col-3">
              <label>Lines #:</label>
              <br>
              <b>{{ order['product_line_count'] }}</b>
            </div>
            <div class="col-3">
              <label>Quantity #:</label>
              <br>
              <b>{{ order['total_quantity_ordered'] }}</b>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from "moment";

    export default {
        name: "OrderDetails",

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
