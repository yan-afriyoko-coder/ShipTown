<template>
    <div>
        <div class="row mb-3 pl-1 pr-1 sticky-top">
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
                    <suggestion-record :record="record"></suggestion-record>
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
    import SuggestionRecord from "./StocktakingPage/SuggestionRecord";

    export default {
        mixins: [loadingOverlay, url, api, helpers],

        components: {
            BarcodeInputField,
            SuggestionRecord,
        },

        data: function() {
            return {
                per_page: 10,
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

                if (this.isMoreThanPercentageScrolled(70))
                {
                    // we double per_page every second page load to avoid hitting the API too hard
                    // and we will limit it to 100-ish per_page
                    if ((this.per_page < 100) && (this.pagesLoaded % 2 === 0)) {
                        this.pagesLoaded = this.pagesLoaded / 2;
                        this.per_page = this.per_page * 2;
                    }

                    this.loadStocktakeSuggestions(++this.pagesLoaded);
                }
            },

            loadRecentStocktakes() {
                let params = {...this.$router.currentRoute.query};
                params['filter[description]'] = 'stocktake';
                params['filter[warehouse_code]'] = this.getUrlParameter('filter[warehouse_code]');
                params['include'] = 'product';
                params['sort'] = '-id';
                params['per_page'] = 2;

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

                let params = {...this.$router.currentRoute.query};
                params['include'] = 'product,inventory,product.tags';
                params['sort'] = '-points,inventory_id';
                params['per_page'] = this.per_page;
                params['page'] = page;

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
