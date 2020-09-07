<template>
    <div class="row mb-0 ml-1 mr-1">
        <div class="col p-0 pl-3">
            <div class="row text-left">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-1">
<!--                            <div class="font-weight-bold text-nowrap">Order #</div>-->
                            <div class="text-nowrap">{{ order['order_number'] }}</div>
                        </div>
                        <div class="col-2">
<!--                            <div class="font-weight-bold text-nowrap">Status</div>-->
                            <div class="text-right text-nowrap">{{ order['status_code'] }}</div>
                        </div>
                        <div class="col-2">
<!--                            <div class="font-weight-bold text-right text-nowrap">Date Placed</div>-->
                            <div class="text-right text-nowrap">{{ order['order_placed_at'] | moment('MMM DD') }}</div>
                        </div>
                        <div class="col-2">
<!--                            <div class="font-weight-bold text-right text-nowrap">Line Count</div>-->
                            <div class="text-right text-nowrap">{{ order['product_line_count'] }}</div>
                        </div>
                        <div class="col-1">
<!--                            <div class="font-weight-bold text-right text-nowrap">Total Quantity</div>-->
                            <div class="text-right text-nowrap">{{ order['total_quantity_ordered'] }}</div>
                        </div>
                        <div class="col-1">
<!--                            <div class="font-weight-bold text-right text-nowrap">Picked</div>-->
                            <div class="text-right text-nowrap">{{ order['is_picked'] }}</div>
                        </div>
                        <div class="col-1">
<!--                            <div class="font-weight-bold text-right text-nowrap">Packed</div>-->
                            <div class="text-right text-nowrap">{{ order['is_packed'] }}</div>
                        </div>
                        <div class="col-2">
<!--                            <div class="font-weight-bold text-right text-nowrap">Packed</div>-->
                            <div class="text-right text-nowrap">{{ order['shipping_number'] }}</div>
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
