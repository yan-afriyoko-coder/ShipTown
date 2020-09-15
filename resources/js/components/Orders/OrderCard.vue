<template>
    <div class="row mb-0 ml-1 mr-1">
        <div class="col p-0 pl-3">
            <div class="row text-left">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-1">
                            <div class="text-nowrap">{{ order['order_number'] }}</div>
                        </div>
                        <div class="col-1">
                            <div class="text-nowrap">{{ order['status_code'] }}</div>
                        </div>
                        <div class="col-2">
                            <div class="text-nowrap">{{ order['order_placed_at'] | moment('MM/DD H:mm') }}</div>
                        </div>
                        <div class="col-2">
                            <div class="text-center text-nowrap">{{ order['product_line_count'] }}</div>
                        </div>
                        <div class="col-1">
                            <div class="text-center text-nowrap">{{ order['total_quantity_ordered'] }}</div>
                        </div>
                        <div class="col-1">
                            <div class="text-center text-nowrap">{{ order['picked_at'] | moment('MM/DD H:mm') }}</div>
                        </div>
                        <div class="col-1">
                            <div class="text-left text-nowrap">{{ order['packer'] ? order['packer']['name'] : '' }}</div>
                        </div>
                        <div class="col-1">
                            <div class="text-center text-nowrap">{{ order['packed_at'] | moment('MM/DD H:mm') }}</div>
                        </div>
                        <div class="col-2">
                            <div class="text-right text-nowrap">
                                <template v-for="shipment in order['order_shipments']">
                                    <div>{{ shipment['shipping_number'] }}</div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "OrderCard",

        props: {
            order: Object,
        },
    }
</script>

<style scoped>
    .col {
        background-color: #ffffff;
        border: 0px solid #76777838;
    }
</style>
