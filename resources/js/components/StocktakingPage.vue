<template>
    <div>
        <div class="row mb-3 pl-1 pr-1">
            <div class="flex-fill">
                <stocktaking-input-field @stocktakeSubmitted="reloadData"></stocktaking-input-field>
            </div>

            <button id="config-button" disabled type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#filterConfigurationModal"><font-awesome-icon icon="cog" class="fa-lg"></font-awesome-icon></button>
        </div>

        <div class="row col d-block font-weight-bold pb-1 text-uppercase small text-secondary align-content-center text-center">Recent Stocktakes</div>

        <swiping-card :disable-swipe-right="true" :disable-swipe-left="true">
            <template v-slot:content>
                <template v-for="itemMovement in recentStocktakes.data">
                    <div class="row">
                        <div class="col-sm-12 col-lg-5">
                            {{ Number(itemMovement['quantity_after']) }} x {{ itemMovement['product']['sku'] }} - {{ itemMovement['product']['name'] }}<br>
                        </div>
                    </div>
                </template>

                <a href="/reports/stocktakes" class="d-block text-center">See more</a>
            </template>
        </swiping-card>

        <div class="row col d-block font-weight-bold pb-1 text-uppercase small text-secondary align-content-center text-center">Stocktake suggestions</div>

        <div v-if="(stocktakeSuggestions !== null) && (stocktakeSuggestions.length === 0)" class="text-center mt-3">
            You're all done! <br>
            No more suggestions found.
        </div>

        <template v-if="stocktakeSuggestions" v-for="record in stocktakeSuggestions">
            <swiping-card :disable-swipe-right="true" :disable-swipe-left="true">
                <template v-slot:content>
                    <div class="row">
                        <div class="col-sm-12 col-lg-5">
                            <product-info-card :product= "record['product']"></product-info-card>
                        </div>

                        <div class="row col-sm-12 col-lg-7 text-right">
                            <div class="col-12 col-md-4 text-left small">
                                <div>in stock: <strong>{{ dashIfZero(Number(record['inventory']['quantity_available'])) }}</strong></div>
                            </div>
                            <div class="col-12 col-md-8 text-right">
                                <number-card label="points" :number="record['points']"></number-card>
                                <text-card label="shelf" :number="record['inventory']['shelf_location']"></text-card>
                            </div>
                        </div>
                    </div>
                </template>
            </swiping-card>
        </template>

        <div class="row col" ref="loadingContainerOverride" style="height: 100px"></div>

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
                pagesLoaded: 0,
                reachedEnd: false,

                recentStocktakes: [],
                stocktakeSuggestions: null,
            };
        },

        mounted() {
            if (! this.currentUser()['warehouse_id']) {
                this.$snotify.error('You do not have warehouse assigned. Please contact administrator', {timeout: 50000});
                return;
            }

            this.getUrlFilterOrSet('filter[warehouse_code]', this.currentUser()['warehouse']['code']);

            window.onscroll = () => this.loadMore();

            this.loadStocktakeSuggestions();
            this.loadRecentStocktakes();

        },

        methods: {
            reloadData() {
                this.loadStocktakeSuggestions();
                this.loadRecentStocktakes();
            },

            loadMore: function () {
                if (this.isLoading) {
                    return;
                }

                if (this.reachedEnd) {
                    return;
                }

                if (this.isMoreThanPercentageScrolled(70)) {
                    this.loadStocktakeSuggestions(++this.pagesLoaded);
                }
            },

            loadRecentStocktakes() {
                const params = {
                    'filter[description]': 'stocktake',
                    'filter[warehouse_id]': Number(this.currentUser()['warehouse_id']),
                    'include': 'product',
                    'sort': '-id',
                    'per_page': 2,
                }

                this.apiGetInventoryMovements(params)
                    .then((response) => {
                        this.recentStocktakes = response.data;
                    })
                    .catch((error) => {
                        this.displayApiCallError(error);
                    });
            },


            loadStocktakeSuggestions(page = 1) {
                this.showLoading();

                if (page === 1) {
                    this.stocktakeSuggestions = null;
                }

                const params = {
                    'filter[warehouse_id]': this.currentUser()['warehouse_id'],
                    'include': 'product,inventory',
                    'sort': '-points,inventory_id',
                    'per_page': 10,
                    'page': page,
                }

                this.apiGetStocktakeSuggestions(params)
                    .then((response) => {
                        if (page === 1) {
                            this.stocktakeSuggestions = [];
                        }
                        this.reachedEnd = response.data.data.length === 0;
                        this.pagesLoaded = page;

                        this.stocktakeSuggestions = this.stocktakeSuggestions.concat(response.data.data);
                    })
                    .catch((error) => {
                        this.displayApiCallError(error);
                    })
                    .finally(() => {
                        this.hideLoading();
                    });
            },
        },
    }
</script>

<style lang="scss" scoped>
</style>
