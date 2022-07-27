<template>
    <div class="container dashboard-widgets">

        <div class="row mb-3 pl-1 pr-1" v-if="currentUser['warehouse'] !== null">
            <div class="flex-fill">
                <barcode-input-field placeholder='Search products using name, sku, alias or command'></barcode-input-field>
            </div>

            <button id="config-button" disabled type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#filterConfigurationModal"><font-awesome-icon icon="cog" class="fa-lg"></font-awesome-icon></button>
        </div>


        <template v-for="record in initial_data">
            <div class="row mb-3">
                <div class="col ml-0 pl-0">
                    <div class="card ml-0 pl-0">
                        <div class="card-body pt-2 pl-2">
                            <div class="row mt-0 small">
                                <div class="col-lg-6">
                                    <div class="text-primary h5">{{ record['product_name'] }}</div>
                                    <div>
                                        sku: <b><a target="_blank" :href="'/products?hide_nav_bar=true&search=' + record['product_sku']">{{ record['product_sku'] }}</a></b>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row pt-1">
                                        <div class="col-6">
                                            <div >
                                                incoming: <b>{{ record['quantity_incoming'] }}</b>
                                            </div>
                                            <div >
                                                warehouse: <b>{{ record['warehouse_quantity'] }}</b>
                                            </div>
                                            <div :class=" record['reorder_point'] <= 0 ? 'bg-warning' : ''">
                                                reorder_point: <b>{{ record['reorder_point'] }}</b>
                                            </div>
                                            <div :class="record['reorder_point'] <= 0 ? 'bg-warning' : ''">
                                                restock_level: <b>{{ record['restock_level'] }}</b>
                                            </div>
                                            <div>
                                                warehouse_code: <b>{{ record['warehouse_code'] }}</b>
                                            </div>
                                        </div>
                                        <div :class="'col-3 text-center' + record['quantity_available'] <= 0 ? 'bg-warning' : '' ">
                                            <small>available</small>
                                            <h3>{{ record['quantity_available'] }}</h3>
                                        </div>
                                        <div class="col-3 text-center">
                                            <small>required</small>
                                            <h3>{{ record['quantity_required'] }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

</template>

<script>
    import loadingOverlay from '../mixins/loading-overlay';
    import OrderCard from "./Orders/OrderCard";
    import url from "../mixins/url";
    import BarcodeInputField from "./SharedComponents/BarcodeInputField";
    import api from "../mixins/api";
    import helpers from "../mixins/helpers";
    import Vue from "vue";

    export default {
        mixins: [loadingOverlay, url, api, helpers],

        props: {
            initial_data: [],
        },

        components: {
        },

        data: function() {
            return {
            };
        },

        mounted() {

        },

        methods: {

        },
    }
</script>

<style lang="scss" scoped>
    //.row {
    //    display: flex;
    //    justify-content: center;
    //    align-items: center;
    //}
</style>
