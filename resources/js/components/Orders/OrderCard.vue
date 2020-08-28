<template>
    <div class="row mb-3 ml-1 mr-1">
        <div class="col p-2 pl-3">
            <div class="row text-left">
                <div class="col-md-4">
                    <div class="text-primary h5">Order # <span class="font-weight-bold"> {{ order.order_number }} </span></div>
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-3 font-weight-bold">Status</div>
                        <div class="col-3 font-weight-bold text-right">Date Placed</div>
                        <div class="col-3 font-weight-bold text-right">Line Count</div>
                        <div class="col-3 font-weight-bold text-right">Total Quantity</div>
                    </div>

                    <div class="row">
                        <div class="col-3">{{ order.status_code }}</div>
                        <div class="col-3 text-right">{{ order.order_placed_at | moment('MMM DD') }}</div>
                        <div class="col-3 text-right">{{ order.product_line_count }}</div>
                        <div class="col-3 text-right">{{ order.total_quantity_ordered }}</div>
                    </div>

                    <div class="row">
                        <div class="col-3 font-weight-bold">Is Picked</div>
                        <div class="col-3 font-weight-bold">Is Packed</div>
                    </div>

                    <div class="row">
                        <div class="col-3">{{ order.is_picked }}</div>
                        <div class="col-3">{{ order.is_packed }}</div>
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
        border: 1px solid #76777838;
    }
</style>
