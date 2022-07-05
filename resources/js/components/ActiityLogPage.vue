<template>
    <div>
        <template v-if="getUrlParameter('hide_nav_bar', false) === false">
            <div class="row mb-3 pl-1 pr-1">
                <div class="flex-fill">
                    <barcode-input-field placeholder="Search products using name, sku, alias or command"
                                         ref="barcode"
                                         :url_param_name="'search'"
                                         @refreshRequest="reloadProducts"
                                         @barcodeScanned="findText"
                    />
                </div>

                <button disabled type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#filterConfigurationModal"><font-awesome-icon icon="cog" class="fa-lg"></font-awesome-icon></button>
            </div>
        </template>

        <template  v-if="activityLog.length === 0 && !isLoading" >
            <div class="row mt-3">
                <div class="col">
                    <div class="alert alert-info" role="alert">
                        No activities found.
                    </div>
                </div>
            </div>
        </template>

        <template v-for="activity in activityLog">
            <tr>
                <td>{{ activity['created_at'] }}</td>
                <td>{{ activity['causer_id'] ? activity['causer']['name'] : 'AutoPilot' }}</td>
                <td>{{ activity['description'] }}</td>
                <td>{{ activity['subject_type'] }}</td>
                <td>{{ activity['subject_id'] }}</td>
            </tr>
        </template>

        <div class="row">
            <div class="col">
                <div ref="loadingContainerOverride" style="height: 100px"></div>
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
                lastPageLoaded: 1,
                lastPage: 1,

                products: [],
                activityLog: [],
            };
        },

        mounted() {
            // window.onscroll = () => this.loadMore();

            this.loadActivityLog(1);
        },

        methods: {
            loadActivityLog: function () {
                this.statusMessageActivity = "Loading activities ...";
                const params = {
                    // 'filter[subject_type]': 'App\\Models\\Product',
                    // 'filter[subject_id]': this.product['id'],
                    'sort': '-id',
                    'include': 'causer',
                    'per_page': 1000
                }

                this.apiGetActivityLog(params)
                    .then(({data}) => {
                        this.activityLog = data.data
                    });
            },

            findText(search) {
                this.setUrlParameter('search', search);
                this.reloadProducts();
            },

            reloadProducts() {
                this.products = [];
                this.loadProductList();
            },

            loadProductList: function(page = 1) {
                this.showLoading();

                const params = {
                    'filter[sku]': this.getUrlParameter('sku'),
                    'filter[search]': this.getUrlParameter('search'),
                    'filter[has_tags]': this.getUrlParameter('has_tags'),
                    'filter[without_tags]': this.getUrlParameter('without_tags'),
                    'sort': this.getUrlParameter('sort', '-quantity'),
                    'include': 'inventory,tags,prices,aliases,inventory.warehouse',
                    'per_page': this.getUrlParameter('per_page', 25),
                    'page': page
                }

                this.apiGetProducts(params)
                    .then(({data}) => {
                        if (page === 1) {
                            this.products = [];
                        }
                        this.products = this.products.concat(data.data);
                        this.lastPage = data.meta.last_page;
                        this.lastPageLoaded = page;
                    })
                    .finally(() => {
                        this.hideLoading();
                    });
                return this;
            },

            loadMore: function () {
                if (this.isMoreThanPercentageScrolled(70) && this.hasMorePagesToLoad() && !this.isLoading) {
                    this.loadProductList(++this.lastPageLoaded);
                }
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
