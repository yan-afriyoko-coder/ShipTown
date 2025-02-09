<template>
    <div>
        <search-and-option-bar-observer/>
        <search-and-option-bar :isStickable="true">
            <stocktake-input @stocktakeSubmitted="reloadData" :placeholder="'test'"></stocktake-input>
            <template v-slot:buttons>
                <top-nav-button v-b-modal="'quick-actions-modal'"/>
            </template>
        </search-and-option-bar>

        <div class="row pl-2 p-1 font-weight-bold text-uppercase small text-secondary">
            <div class="col-6 text-left text-nowrap">
                PRODUCTS > STOCKTAKING
            </div>
            <div class="col-6 text-right text-nowrap">
                <a :href="'/reports/inventory-movements?filter[description]=stocktake'">SEE MORE</a>
            </div>
        </div>

        <template v-if="recentStocktakes.data">
            <div class="text-secondary small text-center" v-if="recentStocktakes.data.length === 0">
                &nbsp; No recent stocktakes found
            </div>

            <div class="row card text-secondary p-1 pl-2 mb-2" v-if="recentStocktakes.data.length > 0">
                <template v-for="itemMovement in recentStocktakes.data">
                        <div>
                            {{ Number(itemMovement['quantity_after']) }} x
                            {{ itemMovement['product']['sku'] }}
                            - {{ itemMovement['product']['name'] }}
                        </div>
                </template>
            </div>
        </template>

        <div class="d-flex justify-content-between align-items-center mt-2 pl-2 p-1 font-weight-bold text-uppercase small text-secondary">
            REPORTS > STOCKTAKE SUGGESTIONS
<!--            <button class="btn btn-sm btn-primary" @click="downloadStocktakeSuggestion">Download</button>-->
        </div>

        <div v-if="(stocktakeSuggestions !== null) && (stocktakeSuggestions.length === 0)" class="text-secondary small text-center mt-3">
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

        <div class="row col" ref="loadingContainerOverride" style="height: 32px"></div>

        <b-modal id="quick-actions-modal" no-fade hide-header @hidden="setFocusElementById('barcode_input')">
            <stocktake-input></stocktake-input>
            <template #modal-footer>
                <b-button variant="secondary" class="float-right" @click="$bvModal.hide('quick-actions-modal');">
                    Cancel
                </b-button>
                <b-button variant="primary" class="float-right" @click="$bvModal.hide('quick-actions-modal');">
                    OK
                </b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
import loadingOverlay from '../mixins/loading-overlay';
import api from "../mixins/api";
import helpers from "../mixins/helpers";
import url from "../mixins/url";
import SuggestionRecord from "./StocktakingPage/SuggestionRecord";

export default {
        mixins: [loadingOverlay, url, api, helpers],

        components: {
            SuggestionRecord,
        },

        data: function() {
            return {
                per_page: 20,
                pagesLoaded: 0,
                reachedEnd: false,

                recentStocktakes: [],
                stocktakeSuggestions: null,
                selectedInventoryId: null,
                warehouse_code: null,
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
                params['filter[product_has_tags]'] = '';
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

                let params = {...this.$router.currentRoute.query};
                params['select'] = 'warehouse_code,product_sku,product_name,quantity_in_stock,reason,points,last_movement_at,last_counted_at,last_sold_at,in_stock_since,inventory_id,product_id,warehouse_id'
                params['filter[warehouse_code]'] = this.getUrlParameter('filter[warehouse_code]');
                params['include'] = 'product,inventory,product.tags,product.prices';
                params['sort'] = this.getUrlParameter('sort', '-points');
                params['per_page'] = this.per_page;
                params['page'] = page;

                this.apiGetStocktakeSuggestions(params)
                    .then((response) => {
                        if (page === 1) {
                            this.stocktakeSuggestions = [];
                            this.stocktakeSuggestions = response.data.data;
                        } else {
                            this.stocktakeSuggestions = this.stocktakeSuggestions.concat(response.data.data);
                        }
                        this.reachedEnd = response.data.data.length === 0;
                        this.pagesLoaded = page;

                    })
                    .catch((error) => {
                        this.displayApiCallError(error);
                    })
                    .finally(() => {
                        this.hideLoading();
                    });
            },

            downloadStocktakeSuggestion() {
                let params = {...this.$router.currentRoute.query};
                params['filter[warehouse_code]'] = this.getUrlParameter('filter[warehouse_code]');
                params['sort'] = this.getUrlParameter('sort', '-points');
                params['filename'] = 'Stocktake Suggestions.csv'

                let routeData = this.$router.resolve({
                    path: '/reports/stocktake-suggestions',
                    query: params
                });

                let routeLink = routeData.href

                window.open(routeLink, '_blank')
            }
        },
    }
</script>

<style lang="scss" scoped>
</style>
