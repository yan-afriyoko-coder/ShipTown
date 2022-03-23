<template>
    <div class="col" v-if="order">



        <div class="row ml-1 mr-1 card">
            <div class="col p-2 pl-2 rounded">
                <div class="row">
                    <h5 class="text-primary">
                        <font-awesome-icon icon="copy" class="fa-xs" role="button" @click="copyToClipBoard(order['order_number'])"></font-awesome-icon>
                        <a :href="'/orders/?search=' + order['order_number']">{{ order['order_number'] }}</a>
                    </h5>
                </div>
                <div class="row align-text-top">

                    <div class="col-5 col-md-4 col-lg-3 align-text-top">
                        <div class="small"> <b> {{ order['status_code'] }} </b> </div>
                    </div>

                    <div class="col text-center">
                        <div class="row ">
                            <div class="col">
                                <small> age </small>
                                <h5> {{ order['age_in_days'] }}</h5>
                            </div>
                            <div class="col">
                                <small> products </small>
                                <h5> {{ order['product_line_count'] }} </h5>
                            </div>
                            <div class="col">
                                <small> quantity </small>
                                <h5> {{ order['total_quantity_ordered'] }} </h5>
                            </div>
                            <div class="col d-none d-sm-block">
                                <small> total </small>
                                <h5>{{ order['total'] }}</h5>
                            </div>
                        </div>
                    </div>

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
