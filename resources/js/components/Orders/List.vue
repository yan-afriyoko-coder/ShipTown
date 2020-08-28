<template>
    <div>
        <div class="row no-gutters mb-3 ml-1 mr-1">
            <div class="col">
                <input ref="search" @focus="doSelectAll" class="form-control" @keyup.enter="loadOrders"
                       v-model="query" placeholder="Search for orders..." />
            </div>
        </div>
        <div class="container">
            <div v-if="total == 0 && !isLoading" class="row" >
                <div class="col">
                    <div class="alert alert-info" role="alert">
                        No orders found.
                    </div>
                </div>
            </div>
            <template v-else class="row">
                <template v-for="order in orders">
                    <order-card :order="order"/>
                </template>
            </template>
        </div>
    </div>
</template>

<script>
    import loadingOverlay from '../../mixins/loading-overlay';
    import OrderCard from "./OrderCard";

    export default {
        mixins: [loadingOverlay],

        components: { 'order-card': OrderCard },

        created() {
            this.getOrderList(this.page);
        },

        mounted() {
            this.$refs.search.focus();
            this.scroll();
        },

        methods: {
            getOrderList: function(page) {

                this.orders = [];
                this.page = page;
                this.last_page = 1;
                this.total = 0;

                return new Promise((resolve, reject) => {
                    this.showLoading();
                    axios.get('/api/orders', {
                        params: {
                            page: page,
                            q: this.query,
                            sort: this.sort,
                            order: this.order,
                        }
                    }).then(({ data }) => {
                        this.orders = this.orders.concat(data.data);
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

            loadOrders(e) {
                this.getOrderList(1)
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
                        this.getOrderList(++this.page);
                    }
                };
            },
        },

        data: function() {
            return {
                query: null,
                sort: 'sku',
                order: 'asc',
                orders: [],
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
