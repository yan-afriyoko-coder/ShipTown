<template>
    <div class="container dashboard-widgets">

        <div class="row mb-3 pl-1 pr-1" v-if="currentUser['warehouse'] !== null">
            <div class="flex-fill">
                <stocktaking-input-field placeholder="Search products using name, sku, alias or command"></stocktaking-input-field>
            </div>

            <button id="config-button" disabled type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#filterConfigurationModal"><font-awesome-icon icon="cog" class="fa-lg"></font-awesome-icon></button>
        </div>

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
    .row {
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
