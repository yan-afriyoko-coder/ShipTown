<template>
    <div>
        <div class="row no-gutters mb-3 ml-1 mr-1">
            <div class="col">
                <barcode-input-field :placeholder="'Scan order number and click enter'" @barcodeScanned="searchText" />
            </div>
        </div>
        <div class="container">
            <div v-if="total === 0 && !isLoading" class="row" >
                <div class="col">
                    <div class="alert alert-info" role="alert">
                        No orders found.
                    </div>
                </div>
            </div>
            <template v-else class="row">
                <div class="col">
                    <template v-for="order in orders">
                        <order-card :order="order"/>
                    </template>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    import loadingOverlay from '../mixins/loading-overlay';
    import OrderCard from "./Orders/OrderCard";
    import url from "../mixins/url";
    import BarcodeInputField from "./SharedComponents/BarcodeInputField";

    export default {
        mixins: [loadingOverlay, url],

        components: {
            'order-card': OrderCard,
            'barcode-input-field': BarcodeInputField,
        },

        created() {
            this.getOrderList(this.page);
        },

        mounted() {
            // this.$refs.search.focus();
            this.scroll();
        },

        methods: {
            searchText(text) {
                this.setUrlParameter('search', text);
                this.loadOrders();
            },

            getOrderList: function(page) {

                const params = {
                    'filter[status]': this.getUrlParameter('status', null),
                    'filter[order_number]': this.getUrlParameter('search', ''),
                    'sort': this.getUrlParameter('sort','-updated_at'),
                    'per_page': this.getUrlParameter('per_page',50),
                    'include': 'packer,order_shipments,order_products,order_products.product,order_comments,order_comments.user',
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
                    let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight >= document.documentElement.offsetHeight;

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
