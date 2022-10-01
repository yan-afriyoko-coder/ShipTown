<template>
    <div>
        <div class="row mb-1 pb-2 p-1 sticky-top bg-light" style="z-index: 10;">
            <div class="flex-fill">
                <barcode-input-field placeholder="Search products using name, sku, alias or command"
                                     ref="barcode"
                                     :url_param_name="'filter[search]'"
                                     @barcodeScanned="findText"
                />
            </div>

            <button disabled type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#configurationModal"><font-awesome-icon icon="cog" class="fa-lg"></font-awesome-icon></button>
        </div>
        <date-selector-widget :dates="{'url_param_name': 'filter[created_at_between]'}"></date-selector-widget>

        <div class="row col d-block font-weight-bold pb-1 text-uppercase small text-secondary align-content-center text-center">Inventory Movements</div>

        <template v-for="record in records">
            <swiping-card :disable-swipe-right="true" :disable-swipe-left="true">
                <template v-slot:content>
                    <div class="row">
                        <div class="col-sm-12 col-lg-5">
                            <product-info-card :product= "record['product']"></product-info-card>
                            <div class="small">at: <strong>{{ record['created_at'] | moment('MMM D HH:mm') }}</strong></div>
                        </div>

                        <div class="row col-sm-12 col-lg-7 text-right">
                            <div class="col-12 col-md-4 text-left small">
                                <div>type: <strong>{{ record['description'] }}</strong></div>
                                <div>by: <strong>{{ record['user'] ? record['user']['name'] : '' }}</strong></div>
                                <div>shelf: <strong>{{ record['inventory']['shelf_location'] }}</strong></div>
                                <div>in stock: <strong>{{ dashIfZero(Number(record['inventory']['quantity_available'])) }}</strong></div>
                            </div>
                            <div class="col-12 col-md-8 text-right">
                                <number-card label="before" :number="record['quantity_before']"></number-card>
                                <number-card label="change" :number="record['quantity_delta']"></number-card>
                                <number-card label="after" :number="record['quantity_after']"></number-card>
                            </div>
                        </div>
                    </div>
                </template>
            </swiping-card>
        </template>

        <div class="row">
            <div class="col">
                <div ref="loadingContainerOverride"></div>
            </div>
        </div>

    </div>
</template>

<script>
    import loadingOverlay from '../mixins/loading-overlay';
    import ProductCard from "./Products/ProductCard";
    import BarcodeInputField from "./SharedComponents/BarcodeInputField";
    import url from "../mixins/url";
    import api from "../mixins/api";
    import helpers from "../mixins/helpers";

    export default {
        mixins: [loadingOverlay, url, api, helpers],

        components: {
            ProductCard,
            BarcodeInputField
        },

        data: function() {
            return {
                pagesLoaded: 0,
                reachedEnd: false,

                records: [],
            };
        },

        mounted() {
            if (this.currentUser()['warehouse']) {
                this.getUrlFilterOrSet('filter[warehouse_code]', this.currentUser()['warehouse']['code']);
            }

            window.onscroll = () => this.loadMore();

            this.loadRecords(1);
        },

        methods: {
            findText(search) {
                this.setUrlParameter('search', search);
                this.reloadProducts();
            },

            reloadProducts() {
                this.products = [];
                this.loadRecords();
            },

            loadRecords: function(page = 1) {
                this.showLoading();

                let params = this.$router.currentRoute.query;
                params['include'] = 'product,inventory,user';
                params['sort'] = '-created_at';
                params['page'] = page;

                this.apiGetInventoryMovements(params)
                    .then(({data}) => {
                        if (page === 1) {
                            this.records = [];
                        }

                        this.reachedEnd = data.data.length === 0;
                        this.pagesLoaded = page;

                        this.records = this.records.concat(data.data);
                    })
                    .finally(() => {
                        this.hideLoading();
                    });
                return this;
            },

            loadMore: function () {
                if (this.isLoading) {
                    return;
                }

                if (this.reachedEnd) {
                    return;
                }

                if (! this.isMoreThanPercentageScrolled(70)) {
                    return;
                }

                this.loadRecords(++this.pagesLoaded);
            },

            hasMorePagesToLoad: function () {
                return this.lastPage > this.lastPageLoaded;
            },
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
