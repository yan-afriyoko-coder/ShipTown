<template>
    <div>
        <div class="row no-gutters mb-3 ml-1 mr-1">
            <div class="col">
                <input ref="search" @focus="doSelectAll" class="form-control" @keyup.enter="loadProducts"
                       v-model="query" placeholder="Search for products..." />
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
    import loadingOverlay from '../../mixins/loading-overlay';
    import ProductCard from "./ProductCard";
    import url from "../../mixins/url";

    export default {
        mixins: [loadingOverlay, url],

        components: { 'product-card': ProductCard },

        created() {
            this.getProductList(this.page);
        },

        mounted() {
            this.$refs.search.focus();
            this.scroll();
        },

        methods: {
            getProductList: function(page) {

                const params = {
                        page: page,
                        q: this.getUrlFilter('query'),
                        sort: this.sort,
                        order: this.order,
                        include: 'inventory'
                }

                this.products = [];
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

        data: function() {
            return {
                query: null,
                sort: 'sku',
                order: 'asc',
                products: [],
                total: 0,
                page: 1,
                last_page: 1,
            };
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
