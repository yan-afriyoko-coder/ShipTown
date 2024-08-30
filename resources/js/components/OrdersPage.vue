<template>
    <div>
        <search-and-option-bar-observer/>
        <search-and-option-bar :isStickable="true">
            <barcode-input-field :input_id="'barcode-input'"  :url_param_name="'search'" @barcodeScanned="findText" placeholder="Search" ref="barcode"/>
            <template v-slot:buttons>
                <top-nav-button v-b-modal="'quick-actions-modal'"/>
            </template>
        </search-and-option-bar>

        <div class="row pl-2 p-0">
            <breadcrumbs></breadcrumbs>
        </div>

        <div class="row" v-if="orders.length === 0 && !isLoading">
            <div class="text-secondary small text-center mt-3">
                No records found<br>
                Click + to create one<br>
            </div>
        </div>

        <template v-for="order in orders">
            <div class="row">
                <div class="col">
                    <order-card :order="order" :expanded="orders.length === 1" @open-modal="openAndLoadDataOrderAddressModal"/>
                </div>
            </div>
        </template>

        <div class="row">
            <div class="col">
                <div ref="loadingContainerOverride" style="height: 32px"></div>
            </div>
        </div>
        <b-modal id="update-order-address-modal" no-fade hide-header @hidden="setFocusElementById('barcode-input')">
            <h5 class="border-bottom pb-1">Edit Order Address</h5>
            <b-form @submit.prevent="onSubmitOrderAddressModal">
                <b-form-group label="Email:" label-for="input-email">
                    <b-form-input id="input-email" type="email" v-model="orderAddress.email" required placeholder="Enter email"></b-form-input>
                </b-form-group>
                <b-form-group label="Phone:" label-for="input-phone">
                    <b-form-input id="input-phone" type="tel" v-model="orderAddress.phone" required placeholder="Enter phone number"></b-form-input>
                </b-form-group>
                <b-form-group label="First Name:" label-for="input-firstname">
                    <b-form-input id="input-firstname" v-model="orderAddress.first_name" required placeholder="Enter first name"></b-form-input>
                </b-form-group>
                <b-form-group label="Last Name:" label-for="input-lastname">
                    <b-form-input id="input-lastname" v-model="orderAddress.last_name" required placeholder="Enter last name"></b-form-input>
                </b-form-group>
                <b-form-group label="Company:" label-for="input-company">
                    <b-form-input id="input-company" v-model="orderAddress.company" placeholder="Enter company name"></b-form-input>
                </b-form-group>
                <b-form-group label="Address 1:" label-for="input-address1">
                    <b-form-input id="input-address1" v-model="orderAddress.address1" required placeholder="Enter address line 1"></b-form-input>
                </b-form-group>
                <b-form-group label="Address 2:" label-for="input-address2">
                    <b-form-input id="input-address2" v-model="orderAddress.address2" placeholder="Enter address line 2"></b-form-input>
                </b-form-group>
                <b-form-group label="Postcode:" label-for="input-postcode">
                    <b-form-input id="input-postcode" v-model="orderAddress.postcode" required placeholder="Enter postcode"></b-form-input>
                </b-form-group>
                <b-form-group label="City:" label-for="input-city">
                    <b-form-input id="input-city" v-model="orderAddress.city" required placeholder="Enter city"></b-form-input>
                </b-form-group>
                <b-form-group label="Country Code:" label-for="input-countrycode">
                    <b-form-input id="input-countrycode" v-model="orderAddress.country_code" required placeholder="Enter country code"></b-form-input>
                </b-form-group>
                <b-form-group label="Country Name:" label-for="input-countryname">
                    <b-form-input id="input-countryname" v-model="orderAddress.country_name" required placeholder="Enter country name"></b-form-input>
                </b-form-group>
                <b-form-group label="Fax:" label-for="input-fax">
                    <b-form-input id="input-fax" v-model="orderAddress.fax" placeholder="Enter fax number"></b-form-input>
                </b-form-group>
                <b-form-group label="Region:" label-for="input-region">
                    <b-form-input id="input-region" v-model="orderAddress.region" placeholder="Enter region"></b-form-input>
                </b-form-group>
                <b-form-group label="State Code:" label-for="input-statecode">
                    <b-form-input id="input-statecode" v-model="orderAddress.state_code" required placeholder="Enter state code"></b-form-input>
                </b-form-group>
                <b-form-group label="State Name:" label-for="input-statename">
                    <b-form-input id="input-statename" v-model="orderAddress.state_name" required placeholder="Enter state name"></b-form-input>
                </b-form-group>
                <b-form-group label="Website:" label-for="input-website">
                    <b-form-input id="input-website" v-model="orderAddress.website" type="url" placeholder="Enter website URL"></b-form-input>
                </b-form-group>
            </b-form>
            <template #modal-footer>
                <b-button variant="secondary" class="float-right" :disabled="isLoading" @click="$bvModal.hide('update-order-address-modal');">
                    Cancel
                </b-button>
                <b-button variant="primary" class="float-right" :disabled="isLoading" @click="onSubmitOrderAddressModal">
                    <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span v-else>OK</span>
                </b-button>
            </template>
        </b-modal>
        <b-modal id="quick-actions-modal" no-fade hide-header @hidden="setFocusElementById('barcode-input')">
            <stocktake-input v-bind:auto-focus-after="100" ></stocktake-input>
            <template #modal-footer>
                <b-button variant="secondary" class="float-right" @click="$bvModal.hide('quick-actions-modal');">
                    Cancel
                </b-button>
                <b-button variant="primary" class="float-right" @click="$bvModal.hide('quick-actions-modal');">
                    OK
                </b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
    import loadingOverlay from '../mixins/loading-overlay';
    import OrderCard from "./Orders/OrderCard";
    import url from "../mixins/url";
    import BarcodeInputField from "./SharedComponents/BarcodeInputField";
    import api from "../mixins/api";
    import helpers from "../mixins/helpers";
    import Vue from "vue";
    import Breadcrumbs from "./Reports/Breadcrumbs.vue";

    export default {
        mixins: [loadingOverlay, url, api, helpers],

        components: {
            Breadcrumbs,
            'order-card': OrderCard,
            'barcode-input-field': BarcodeInputField,
        },

        data: function() {
            return {
                per_page: 20,
                pagesLoaded: 0,
                reachedEnd: false,
                searchText: '',
                orders: [],
                isLoading: false,

                orderAddress: {
                    id : '',
                    email: '',
                    phone: '',
                    first_name: '',
                    last_name: '',
                    company: '',
                    address1: '',
                    address2: '',
                    postcode: '',
                    city: '',
                    country_code: '',
                    country_name: '',
                    fax: '',
                    region: '',
                    state_code: '',
                    state_name: '',
                    website: ''
                }
            };
        },

        mounted() {
            this.getUrlFilterOrSet('created_between', '-4 months,now');
            this.getUrlFilterOrSet('warehouse_id', Vue.prototype.$currentUser['warehouse_id']);
            this.per_page = this.getUrlParameter('per_page', 20);

            window.onscroll = () => this.loadMore();

            this.reloadOrders();
        },

        methods: {
            findText(param) {
                this.setUrlParameter('search', param);
                this.orders = [];
                this.reloadOrders();
            },

            openAndLoadDataOrderAddressModal(orderId) {
                this.fetchOrderAddress(orderId);
                this.$bvModal.show('update-order-address-modal');
            },

            async fetchOrderAddress(orderId) {
                const order = this.orders.find(order => order.shipping_address.id === orderId);
                if (order && order.shipping_address) {
                    this.orderAddress = {
                        id: order.shipping_address.id || '',
                        email: order.shipping_address.email || '',
                        phone: order.shipping_address.phone || '',
                        first_name: order.shipping_address.first_name || '',
                        last_name: order.shipping_address.last_name || '',
                        company: order.shipping_address.company || '',
                        address1: order.shipping_address.address1 || '',
                        address2: order.shipping_address.address2 || '',
                        postcode: order.shipping_address.postcode || '',
                        city: order.shipping_address.city || '',
                        country_code: order.shipping_address.country_code || '',
                        country_name: order.shipping_address.country_name || '',
                        fax: order.shipping_address.fax || '',
                        region: order.shipping_address.region || '',
                        state_code: order.shipping_address.state_code || '',
                        state_name: order.shipping_address.state_name || '',
                        website: order.shipping_address.website || ''
                    };
                } else {
                    console.error('Order not found or Order address is missing');
                }
            },

            onSubmitOrderAddressModal() {
                this.isLoading = true;
                this.apiUpdateOrderAddress(this.orderAddress.id, this.orderAddress)
                    .then(response => {
                        console.log(response);
                        this.$bvModal.hide('update-order-address-modal');
                        this.reloadOrders();
                    })
                    .catch(error => {
                        this.displayApiCallError(error);
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
            },

            reloadOrders(e) {
                this.loadOrderList();
            },

            loadOrderList: function(page = 1) {
                if (page === 1) {
                    this.orders = [];
                }

                this.showLoading();

                this.page = page;

                const params = {
                    'filter[status]': this.getUrlParameter('status'),
                    'filter[search]': this.getUrlParameter('search'),
                    'filter[has_tags]': this.getUrlParameter('has_tags'),
                    'filter[without_tags]': this.getUrlParameter('without_tags'),
                    'filter[age_in_days]': this.getUrlParameter('age_in_days'),
                    'filter[is_active]': this.getUrlParameter('is_active'),
                    'filter[is_on_hold]': this.getUrlParameter('is_on_hold'),
                    'filter[packed_between]': this.getUrlParameter('packed_between'),
                    'filter[packer_user_id]': this.getUrlParameter('packer_user_id'),
                    'filter[shipping_method_code]': this.getUrlParameter('shipping_method'),
                    'filter[created_between]': this.getUrlParameter('created_between'),
                    'sort': this.getUrlParameter('sort','-order_placed_at'),
                    'per_page': this.per_page,
                    'include': 'order_comments,order_comments.user,packer,order_products_totals,tags,shipping_address,billing_address,order_shipments',
                    'page': page,
                };

                this.apiGetOrders(params)
                    .then(({ data }) => {
                        this.orders = this.orders.concat(data.data);
                        this.reachedEnd = data.data.length === 0;
                        this.pagesLoaded = page;
                    })
                    .catch((error) => {
                        this.displayApiCallError(error)
                    })
                    .finally(() => {
                        this.hideLoading();
                    });

                return this;
            },

            loadMore: function () {
                if (this.isMoreThanPercentageScrolled(70) && this.hasMorePagesToLoad() && !this.isLoading) {
                    this.loadOrderList(++this.pagesLoaded);
                }
            },

            hasMorePagesToLoad: function () {
                return this.reachedEnd === false;
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
