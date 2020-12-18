<template>
    <div>

        <div class="row no-gutters mb-3 ml-1 mr-1">
            <div class="col">
                <input ref="search" class="form-control" @keyup.enter="doSearch" v-model="filters['search']" placeholder="Search for products..." />
            </div>
        </div>

        <div v-if="products.length === 0" class="row" >
            <div class="col">
                <div class="alert alert-info" role="alert">
                    No products found.
                </div>
            </div>
        </div>

        <template v-for="product in products">
            <product-card :product="product"/>
        </template>

    </div>
</template>

<script>
    import loadingOverlay from '../mixins/loading-overlay';
    import ProductCard from "./Products/ProductCard";
    import url from "../mixins/url";
    import api from "../mixins/api";
    import helpers from "../mixins/helpers";

    export default {
        mixins: [loadingOverlay, url, api, helpers],

        components: {
            'product-card': ProductCard,
        },

        data: function() {
            return {
                filters: {},
                products: [],
                lastPageLoaded: 1,
                lastPage: 1,
            };
        },

        mounted() {
            this.filters['search'] = this.getUrlParameter('search');

            window.onscroll = () => this.loadMore();

            this.loadProductList(1);
        },

        methods: {
            loadProductList: function(page) {
                const params = {
                    'filter[sku]': this.getUrlParameter('sku'),
                    'filter[search]': this.getUrlParameter('search'),
                    'filter[has_tags]': this.getUrlParameter('has_tags'),
                    'filter[without_tags]': this.getUrlParameter('without_tags'),
                    'sort': this.getUrlParameter('sort', '-quantity'),
                    'include': 'inventory,tags',
                    'page': page
                }

                this.showLoading()
                    .updateUrl(this.filters);

                if (page === 1) {
                    this.products = [];
                    this.lastPageLoaded = 1;
                }

                this.apiGetProducts(params)
                    .then(({data}) => {
                        this.products = this.products.concat(data.data);
                        this.lastPage = data.last_page;
                        this.lastPageLoaded = page;
                    })
                    .finally(() => {
                        this.hideLoading()
                            .setFocus(this.$refs.search, true, true)
                    });

                return this;
            },

            doSearch: function() {
                this.loadProductList(1);
            },

            loadMore: function () {
                if (this.isBottomOfTheWindow() && this.hasMorePagesToLoad()) {
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
