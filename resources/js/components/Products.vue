<template>
    <div>
        <div class="row mb-2 no-gutters">
            <div class="col">
                <input ref="search" @focus="handleSearchFocus" class="form-control" @keyup.enter="handleSearchEnter" v-model="query" placeholder="Search for products..." />
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
                <div class="row mb-3">
                    <div class="col">
                        <div class="row">
                            <div class="col-3">
                                <div class="row header-row text-left">
                                    <div class="col">SKU</div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="row header-row text-left">
                                    <div class="col">NAME</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="row header-row">
                                    <div class="col-3 text-left">Location</div>
                                    <div class="col-3 text-left">In Stock</div>
                                    <div class="col-2 text-left">Reserved</div>
                                    <div class="col-2 text-left">Available</div>
                                    <div class="col-2 text-left">Shelf</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <template v-for="product in products">
                    <div class="row mb-3">
                        <div class="col">
                            <div class="row">
                                <div class="col-3 text-left">
                                    <div class="row">
                                        <div>{{ product.sku }}</div>
                                    </div>
                                </div>
                                <div class="col-3 text-left">
                                    <div class="row text-left">
                                        <div class="row text-left">{{ product.name }}</div>
                                    </div>
                                </div>
                                <div class="col-6 text-right">
                                        <div class="row" v-for="warehouse_inventory in product.inventory">
                                            <div class="col-3">{{ warehouse_inventory.location_id }}</div>
                                            <div class="col-3">{{ warehouse_inventory.quantity }}</div>
                                            <div class="col-2">{{ warehouse_inventory.quantity_reserved }}</div>
                                            <div class="col-2">{{ warehouse_inventory.quantity - warehouse_inventory.quantity_reserved }}</div>
                                            <div class="col-2">{{ warehouse_inventory.shelve_location }}</div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </template>
        </div>
    </div>
</template>

<script>
    import loadingOverlay from '../mixins/loading-overlay';

    export default {
        mixins: [loadingOverlay],

        components: {  },

        created() {
            this.loadProductList(this.page);
        },

        mounted() {
            this.$refs.search.focus();
            this.scroll();
        },

        methods: {
            loadProductList: function(page) {
                return new Promise((resolve, reject) => {
                    this.showLoading();
                    axios.get('/api/products', {
                        params: {
                            page: page,
                            q: this.query,
                            sort: this.sort,
                            order: this.order,
                        }
                    }).then(({ data }) => {
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

            handleSearchEnter(e) {
                this.products = [];
                this.page = 1;
                this.last_page = 1;
                this.total = 0;
                this.loadProductList(1).then(this.handleSearchFocus);
            },

            handleSearchFocus() {
                if (this.query) {
                    setTimeout(() => { document.execCommand('selectall', null, false); });
                }
            },

            scroll (person) {
                window.onscroll = () => {
                    let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;

                    if (bottomOfWindow && this.last_page > this.page) {
                        this.loadProductList(++this.page);
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
    .col {
        background-color: #ffffff;
    }

    .header-row > div, .col {
        border: 1px solid #76777838;
    }

    .header-row > div {
        background-color: #76777838;
    }

    .row-product-name .col {
        padding: 10px;
    }

    .sku-col {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .row {
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
