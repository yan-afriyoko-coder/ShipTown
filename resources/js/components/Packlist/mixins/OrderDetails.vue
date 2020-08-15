<template>
    <div class="row mb-3 ml-1 mr-1">
        <div class="col-3">
            <div>Order #: </div>
            <div class="font-weight-bold">{{ order['order_number'] }}</div>
        </div>
        <div class="col-3">
            <div>Date:</div>
            <div class="font-weight-bold">{{ formatDateMMMDD(order['order_placed_at']) }}</div>
        </div>
        <div class="col-3">
            <div>Lines #:</div>
            <div class="font-weight-bold">{{ order['product_line_count'] }}</div>
        </div>
        <div class="col-3">
            <div>Quantity #:</div>
            <div class="font-weight-bold">{{ order['total_quantity_ordered'] }}</div>
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
