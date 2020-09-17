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




                <div class="row mb-0 ml-1 mr-1">
                    <div class="col p-0 pl-3">
                        <div class="row text-left">
                            <div class="col-md-12">

                                <table>
                                    <thead>
                                        <tr class="font-weight-bold h6">
                                            <th class="text-nowrap pr-5">Order #</th>
                                            <th class="text-nowrap pr-8">StatusCode</th>
                                            <th class="text-nowrap pl-2 pr-8">Total</th>
                                            <th class="text-nowrap pl-3">Total Paid</th>
                                            <th class="text-center pl-3 text-nowrap">Line Count</th>
                                            <th class="text-center pl-3 text-nowrap">Total Quantity</th>
                                            <th class="text-nowrap pl-3">Date Placed</th>
                                            <th class="text-center pl-3 text-nowrap">Picked</th>
                                            <th class="text-center text-nowrap">Packed At</th>
                                            <th class="text-nowrap">Packer</th>
                                            <th class="text-nowrap">Shipping No</th>
                                            <th class="text-nowrap">Products</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <template v-for="order in orders">
                                            <order-card :order="order"/>
                                        </template>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>



            </template>
        </div>
    </div>
</template>

<script>
    import loadingOverlay from '../../mixins/loading-overlay';
    import OrderCard from "./OrderCard";
    import url from "../../mixins/url";

    export default {
        mixins: [loadingOverlay, url],

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

                const params = {
                    'filter[status]': this.getUrlParameter('status', null),
                    'sort': this.getUrlParameter('sort','-updated_at'),
                    'per_page': this.getUrlParameter('per_page',50),
                    'include': 'packer,order_shipments,order_products',
                    page: page,
                    q: this.query,
                };

                // this.orders = [];
                this.page = page;
                this.last_page = 1;
                this.total = 0;

                return new Promise((resolve, reject) => {
                    this.showLoading();
                    axios.get('/api/orders', {
                        params: params
                    }).then(({ data }) => {
                        console.log(data.data);
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
                this.orders = [];
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
