<template>
    <div>
        <div class="row mb-2 no-gutters">
            <div class="col">
                <input ref="search" @focus="handleSearchFocus" class="form-control" @keyup.enter="handleSearchEnter" v-model="query" placeholder="Search for products..." />
            </div>
        </div>
        <div class="container">        
            <div v-if="productsData.total == 0 && !isLoading" class="row" >
                <div class="col">
                    <div class="alert alert-info" role="alert">
                        No products found.
                    </div>
                </div>
            </div>
            <template v-else class="row">
                <template v-for="product in productsData.data">
                    <Product v-for="stock in product.inventory" :product="product" :stock="stock" :key="stock.id" />
                </template>
            </template>
        </div>
    </div>    
</template>

<script>
    import loadingOverlay from '../mixins/loading-overlay';
    import Product from './Product';

    export default {
        mixins: [loadingOverlay],

        components: { Product },

        created() {
            this.loadProductList(1);
        },

        mounted() {
            this.$refs.search.focus();
        },

        methods: {
            loadProductList: function(page) {
                this.showLoading();
                axios.get('/api/inventory', {
                    params: {
                        page: page,
                        q: this.query,
                        sort: this.sort,
                        order: this.order,
                    }
                }).then(({ data }) => {
                    this.productsData = data;
                }).then(() => {
                    this.hideLoading();
                    this.$refs.search.focus();
                });
            },

            handleSearchEnter(e) {
                this.loadProductList(1);
            },

            handleSearchFocus() {
                if (this.query) {
                    setTimeout(() => { document.execCommand('selectall', null, false); });
                }
            },
        },

        data: function() {
            return {
                query: null,
                sort: 'sku',
                order: 'asc',
                productsData: {
                    data: [],
                    total: 0,
                    per_page: 100,
                    current_page: 1,
                },
            };
        },
    }
</script>
