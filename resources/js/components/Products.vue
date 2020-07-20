<template>
    <div>
        <div class="row no-gutters mb-3 ml-1 mr-1">
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
                <template v-for="product in products">

                    <div class="row mb-3 ml-1 mr-1">
                        <div class="col p-2 pl-3">
                            <div class="row text-left">
                                <div class="col-md-6">
                                    <div class="text-primary h4">{{ product.name }}</div>
                                    <div class="text-secondary h5">sku: <span class="font-weight-bold"> {{ product.sku }} </span></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-3 font-weight-bold">Location</div>
                                        <div class="col-3 font-weight-bold">In Stock</div>
                                        <div class="col-2 font-weight-bold">Reserved</div>
                                        <div class="col-2 font-weight-bold">Available</div>
                                        <div class="col-2 font-weight-bold">Shelf</div>
                                    </div>
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
