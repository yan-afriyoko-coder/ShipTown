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

        <div class="row pl-2 p-0">
            <div class="col-6 text-left align-bottom pb-0 m-0 font-weight-bold text-uppercase small text-secondary">
                Inventory Movements
            </div>
            <div class="col-6 text-nowrap">
                <date-selector-widget :dates="{'url_param_name': 'filter[created_at_between]'}"></date-selector-widget>
            </div>
        </div>

        <template v-for="record in records">
            <swiping-card :disable-swipe-right="true" :disable-swipe-left="true">
                <template v-slot:content>
                    <div class="row p-0 h-100">
                        <div class="col-12 col-lg-5 align-text-top">
                            <product-info-card :product= "record['product']"></product-info-card>
                        </div>

                        <div class="row col-sm-12 col-lg-7 text-right">
                            <div class="col-12 col-md-3">
                                <table class="table-borderless small text-left text-nowrap">
                                    <tr>
                                         <td>at:</td>
                                        <td class="pl-1">{{ record['created_at'] | moment('MMM D HH:mm') }}</td>
                                    </tr>
                                    <tr>
                                        <td>type:</td>
                                        <td class="pl-1">{{ record['description'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>by:</td>
                                        <td class="pl-1">{{ record['user'] ? record['user']['name'] : '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>shelf:</td>
                                        <td class="pl-1">{{ record['inventory']['shelf_location'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>in stock:</td>
                                        <td class="pl-1">{{ dashIfZero(Number(record['inventory']['quantity_available'])) }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-12 col-md-9 text-right">
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

    tr {
        padding: 0 !important;
        margin: 0 !important;
    }

    td {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        margin-bottom: 0 !important;
        margin-top: 0 !important;
    }
</style>
