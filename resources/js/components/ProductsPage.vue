<template>
    <div>
        <div class="row no-gutters mb-3 ml-1 mr-1">
            <div class="col">
                <input ref="search" @focus="doSelectAll" class="form-control" @keyup.enter="doSearch"
                       v-model="filters['search']" placeholder="Search for products..." />
            </div>
        </div>
        <div class="container">
            <div v-if="total == 0 && !isLoading" class="row" >
                <div class="col">
                    <div class="alert alert-info" role="alert">
                        No products found.
                    </div>
                </div>
            </div>
            <template v-else class="row">
                <template v-for="product in products">
                    <product-card :product="product"/>
                </template>
            </template>
        </div>
    </div>
</template>

<script>
    import loadingOverlay from '../mixins/loading-overlay';
    import ProductCard from "./Products/ProductCard";
    import url from "../mixins/url";

    export default {
        mixins: [loadingOverlay, url],

        components: { 'product-card': ProductCard },

        data: function() {
            return {
                filters: {
                },
                products: [],
                total: 0,
                page: 1,
                last_page: 1,
            };
        },

        created() {
            this.getProductList(1);
        },

        mounted() {
            this.$refs.search.focus();
            this.scroll();
        },

        methods: {
            doSearch: function() {
                this.products = [];
                this.updateUrl(this.filters);

                this.$refs.search.readOnly = true;
                this.$refs.search.focus();
                this.$refs.search.readOnly = false;

                setTimeout(() => { document.execCommand('selectall', null, false); });

                this.getProductList(1);
            },

            getProductList: function(page) {
                const params = {
                    page: page,
                    'filter[sku]': this.getUrlParameter('sku'),
                    'filter[search]': this.getUrlParameter('search'),
                    q: this.getUrlFilter('query'),
                    sort: '-quantity',
                    include: 'inventory'
                }

                this.page = page;
                this.last_page = 1;
                this.total = 0;

                return new Promise((resolve, reject) => {
                    this.showLoading();
                    axios.get('/api/products', {params: params})
                        .then(({ data }) => {
                            this.products = this.products.concat(data.data);
                            this.total = data.total;
                            this.last_page = data.last_page;
                            resolve(data);
                        })
                        .catch(reject)
                        .then(() => {
                            this.hideLoading();
                        });
                });
            },

            loadProducts(e) {
                this.products = [];
                this.setUrlFilter('query', this.query);
                this.getProductList(1)
                    .then(this.doSelectAll);
            },

            doSelectAll() {
                if (this.query) {
                    setTimeout(() => { document.execCommand('selectall', null, false); });
                }
            },

            scroll (person) {
                window.onscroll = () => {
                    let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;

                    if (bottomOfWindow && this.last_page > this.page) {
                        this.getProductList(++this.page);
                    }
                };
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
