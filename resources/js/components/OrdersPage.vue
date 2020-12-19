<template>
    <div>

        <div class="row no-gutters mb-3 ml-1 mr-1">
            <div class="col">
                <barcode-input-field :placeholder="'Scan order number and click enter'" @barcodeScanned="searchText" />
            </div>
        </div>

        <div class="row" v-if="orders.length === 0 && !isLoading">
            <div class="col">
                <div class="alert alert-info" role="alert">
                    No orders found.
                </div>
            </div>
        </div>

        <template v-for="order in orders">
            <div class="row">
                <div class="col">
                    <order-card :order="order" :expanded="orders.length === 1"/>
                </div>
            </div>
        </template>

    </div>
</template>

<script>
    import loadingOverlay from '../mixins/loading-overlay';
    import OrderCard from "./Orders/OrderCard";
    import url from "../mixins/url";
    import BarcodeInputField from "./SharedComponents/BarcodeInputField";
    import api from "../mixins/api";
    import helpers from "../mixins/helpers";

    export default {
        mixins: [loadingOverlay, url, api, helpers],

        components: {
            'order-card': OrderCard,
            'barcode-input-field': BarcodeInputField,
        },

        data: function() {
            return {
                orders: [],
                total: 0,
                page: 1,
                last_page: 1,
            };
        },

        mounted() {
            this.loadOrderList(this.page);
            this.scroll();
        },

        methods: {
            searchText(text) {
                this.setUrlParameter('search', text);
                this.reloadOrders();
            },

            reloadOrders(e) {
                this.orders = [];
                this.loadOrderList(1);
            },

            loadOrderList: function(page) {
                this.showLoading();

                this.page = page;
                this.last_page = 1;
                this.total = 0;

                const params = {
                    'filter[status]': this.getUrlParameter('status'),
                    'filter[search]': this.getUrlParameter('search'),
                    'sort': this.getUrlParameter('sort','-updated_at'),
                    'per_page': this.getUrlParameter('per_page'),
                    'include': 'order_comments,order_comments.user',
                    'page': page,
                };

                this.apiGetOrders(params)
                    .then(({ data }) => {
                        this.orders = this.orders.concat(data.data);
                        this.total = data.meta.total;
                        this.last_page = data.meta.last_page;
                    })
                    .finally(() => {
                        this.hideLoading()
                            .setFocus(this.$refs.search, true, true);
                    })

                return this;
            },

            scroll (person) {
                window.onscroll = () => {
                    let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight >= document.documentElement.offsetHeight;

                    if (bottomOfWindow && this.last_page > this.page) {
                        this.loadOrderList(++this.page);
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
