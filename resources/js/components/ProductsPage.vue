<template>
    <div>
        <search-and-option-bar-observer/>
        <search-and-option-bar :isStickable="true">
            <barcode-input-field
                :input_id="'barcode_input'"
                placeholder="Search"
                ref="barcode"
                :url_param_name="'search'"
                @refreshRequest="reloadProductList"
                @barcodeScanned="findText"
            />
            <template v-slot:buttons>
                <button @click="showNewProductModal" type="button" class="btn btn-primary ml-2"><font-awesome-icon icon="plus" class="fa-lg"></font-awesome-icon></button>
                <top-nav-button v-b-modal="'quick-actions-modal'"/>
            </template>
        </search-and-option-bar>

        <div class="row pl-2 p-0">
            <breadcrumbs></breadcrumbs>
        </div>

        <template v-if="isLoading === false && products !== null && products.length === 0" >
            <div class="text-secondary small text-center mt-3">
                No records found<br>
                Click + to create one<br>
            </div>
        </template>

        <template v-if="products" v-for="product in products">
            <div class="row p-1">
                <div class="col">
                    <product-card :product="product" :expanded="products.length === 1"/>
                </div>
            </div>
        </template>

        <div class="row">
            <div class="col">
                <div ref="loadingContainerOverride" style="height: 32px"></div>
            </div>
        </div>

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
    import ProductCard from "./Products/ProductCard";
    import BarcodeInputField from "./SharedComponents/BarcodeInputField";
    import url from "../mixins/url";
    import api from "../mixins/api";
    import helpers from "../mixins/helpers";
    import Modals from "../plugins/Modals";
    import Breadcrumbs from "./Reports/Breadcrumbs.vue";

    export default {
        mixins: [loadingOverlay, url, api, helpers],

        components: {
            Breadcrumbs,
            ProductCard,
            BarcodeInputField
        },

        data: function() {
            return {
                pagesLoadedCount: 1,
                reachedEnd: false,
                products: null,
                per_page: 20,
                scroll_percentage: 70,
            };
        },

        mounted() {
            window.onscroll = () => this.loadMore();

            Modals.EventBus.$on('hide::modal::new-product-modal', (newProduct) => {
                this.setUrlParameter('search', newProduct.sku);
                this.reloadProductList();
                this.setFocusElementById('barcode_input');
            });

            this.reloadProductList();
        },

        methods: {
            onScan (decodedText, decodedResult) {
                // handle the message here :)
                this.notifySuccess(decodedText);
                this.notifySuccess(decodedResult);
            },

            findText(search) {
                this.setUrlParameter('search', search);
                this.reloadProductList();
            },

            reloadProductList() {
                this.products = null;

                if (this.getUrlParameter('search')) {
                    this.findProductsWithExactSku();
                }

                this.findProductsContainingSearchText();
            },

            findProductsContainingSearchText: function(page = 1) {
                this.showLoading();

                const params = { ...this.$router.currentRoute.query};
                params['filter[search]'] = this.getUrlParameter('sku') ?? this.getUrlParameter('search');
                params['filter[has_tags]'] = this.getUrlParameter('has_tags');
                params['filter[without_tags]'] = this.getUrlParameter('without_tags');
                params['include'] = 'inventory,tags,prices,aliases,inventory.warehouse,inventoryMovementsStatistics,inventoryTotals';
                params['per_page'] = this.per_page;
                params['page'] = page;
                params['sort'] = this.getUrlParameter('sort', '-quantity');

                this.apiGetProducts(params)
                    .then(({data}) => {
                        this.products = this.products ? this.products.concat(data.data) : data.data
                        this.reachedEnd = data.data.length === 0;
                        this.pagesLoadedCount = page;

                        this.scroll_percentage = (1 - this.per_page  / this.products.length) * 100;
                        this.scroll_percentage = Math.max(this.scroll_percentage, 70);


                        // Remove duplicate product if there are two products with the same sku (possible because of exact search)
                        if (this.products.length > 1  && this.products[0].sku === this.products[1].sku) {
                            this.products.shift();
                        }
                    })
                    .catch((error) => {
                        this.displayApiCallError(error);
                    })
                    .finally(() => {
                        this.hideLoading();
                    });
                return this;
            },

            showNewProductModal() {
                this.$modal.showUpsertProductModal();
            },

            findProductsWithExactSku: function() {
                const params = { ...this.$router.currentRoute.query};
                params['filter[sku_or_alias]'] = this.getUrlParameter('sku') ?? this.getUrlParameter('search');
                params['include'] = 'inventory,tags,prices,aliases,inventory.warehouse,inventoryMovementsStatistics,inventoryTotals';
                params['per_page'] = 1;

                this.apiGetProducts(params)
                    .then(({data}) => {
                        if (data.data.length === 0) {
                            return;
                        }

                        this.products = this.products ? this.products.concat(data.data) : data.data

                        // Remove duplicate product if there are two products with the same sku (possible because of exact search)
                        if (this.products.length > 1  && this.products[0].sku === this.products[1].sku) {
                            this.products.shift();
                        }
                    })
                    .catch((error) => {
                        this.displayApiCallError(error);
                    });
            },

            loadMore: function () {
                if (this.isMoreThanPercentageScrolled(this.scroll_percentage) && this.hasMorePagesToLoad() && !this.isLoading) {
                    this.findProductsContainingSearchText(++this.pagesLoadedCount);
                }
            },

            hasMorePagesToLoad: function () {
                return this.reachedEnd === false;
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
