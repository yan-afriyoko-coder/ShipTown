<template>
    <div>
        <div class="row mb-3 pl-1 pr-1">
            <div class="flex-fill">
                <stocktaking-input-field @stocktakeSubmitted="reloadData"></stocktaking-input-field>
            </div>

            <button id="config-button" disabled type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#filterConfigurationModal"><font-awesome-icon icon="cog" class="fa-lg"></font-awesome-icon></button>
        </div>

        <div class="row" v-if="isLoading">
            <div class="col">
                <div ref="loadingContainerOverride" style="height: 100px"></div>
            </div>
        </div>


        <div class="row" >
            <div class="col">
                <table class="fullWidth w-100">
                    <thead>
                        <tr>
                            <th colspan="3">Stocktake suggestions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="suggestion in stocktakeSuggestions.data">
                            <td>{{ suggestion['product']['sku'] }}</td>
                            <td>{{ suggestion['product']['name'] }}</td>
                            <td class="text-right">{{ suggestion['quantity'] }}</td>
                            <td class="text-right">{{ suggestion['shelf_location'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <br>

        <div class="row" >
            <div class="col">
                <table class="fullWidth w-100">
                    <thead>
                    <tr>
                        <th colspan="2">Recent stocktakes</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="itemMovement in recentStocktakes.data" class="pb-3">
                        <td>
                            {{ itemMovement['product']['name'] }}<br>
                            <small>
                                {{ itemMovement['product']['sku'] }}
                            </small>
                        </td>
                        <td class="text-right">{{ itemMovement['quantity_after'] }}</td>
<!--                        <td class="text-right">{{ itemMovement['inventory']['shelf_location'] }}</td>-->
                    </tr>
                    <tr class="pb-3">
                        <td colspan="2" class="text-center">
                            <a href="/reports/stocktakes">See more</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</template>

<script>
    import loadingOverlay from '../mixins/loading-overlay';
    import BarcodeInputField from "./SharedComponents/BarcodeInputField";
    import api from "../mixins/api";
    import helpers from "../mixins/helpers";
    import url from "../mixins/url";

    export default {
        mixins: [loadingOverlay, url, api, helpers],

        components: {
            BarcodeInputField
        },

        data: function() {
            return {
                inventory: null,
                quantity: null,
                recentStocktakes: [],
                stocktakeSuggestions: [],
            };
        },

        mounted() {
            this.reloadData();

            if (! this.currentUser()['warehouse_id']) {
                this.$snotify.error('You do not have warehouse assigned. Please contact administrator', {timeout: 50000});
                return;
            }

            this.$root.$on('bv::modal::hidden', (bvEvent, modalId) => {
                this.setFocusElementById(300, 'barcodeInput', true, true)
            })

            this.$root.$on('bv::modal::show', (bvEvent, modalId) => {
                // we need to disable it otherwise b-modal might return focus on it too quickly
                // and on screen keyboard will stay visible
                document.getElementById('barcodeInput').readOnly = true;
            })

            this.$root.$on('bv::modal::shown', (bvEvent, modalId) => {
                this.setFocusElementById(100, 'quantity-request-input', true, false)
            })
        },

        methods: {
            reloadData() {
                this.loadRecentStocktakes();
                this.loadStocktakeSuggestions();
            },

            loadRecentStocktakes() {
                const params = {
                    'filter[description]': 'stocktake',
                    'filter[warehouse_id]': this.currentUser()['warehouse_id'],
                    'include': 'product,inventory',
                    'sort': '-id',
                    'per_page': 10,
                }

                this.apiGetInventoryMovements(params)
                    .then((response) => {
                        this.recentStocktakes = response.data;
                    })
                    .catch((error) => {
                        this.displayApiCallError(error);
                    });
            },


            loadStocktakeSuggestions() {
                const params = {
                    'filter[warehouse.id]': this.currentUser()['warehouse_id'],
                    'include': 'product,warehouse',
                    'sort': 'points',
                    'per_page': 10,
                }

                this.apiGetStocktakeSuggestions(params)
                    .then((response) => {
                        this.stocktakeSuggestions = response.data;
                    })
                    .catch((error) => {
                        this.displayApiCallError(error);
                    });
            },
        },
    }
</script>

<style lang="scss" scoped>
</style>
