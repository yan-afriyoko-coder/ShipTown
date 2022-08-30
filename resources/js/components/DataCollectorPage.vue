<template>
    <div>
        <div class="row mb-3 pl-1 pr-1 bg-white">
            <div class="flex-fill">
                <product-count-request-input-field @quantityRequestResponse="onProductCountRequestResponse" placeholder="Scan sku or alias"></product-count-request-input-field>
            </div>

            <button type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#filterConfigurationModal"><font-awesome-icon icon="cog" class="fa-lg"></font-awesome-icon></button>
        </div>

        <template v-for="record in data">
            <swiping-card :disable-swipe-right="true" :disable-swipe-left="true">
                <template v-slot:content>
                    <div class="row">
                        <div class="col-sm-12 col-lg-6 ">
                            <div class="text-primary h5">{{ record['product']['name'] }}</div>
                            <div>
                                product: <b><a target="_blank" :href="'/products?sku=' + record['product']['sku']">{{ record['product']['sku'] }}</a></b>
                            </div>
                        </div>

                        <div class="row-cols col-sm-12 col-lg-6 text-right">
                            <number-card-col label="requested" :number="record['quantity_expected']" v-if="record['quantity_expected']"></number-card-col>
                            <number-card-col label="scanned" :number="record['quantity_collected']" v-bind:class="{ 'bg-warning': record['quantity_collected'] > record['quantity_expected'] }"></number-card-col>
                            <number-card-col label="to scan" :number="record['quantity_required']" v-if="record['quantity_expected']"></number-card-col>
                        </div>
                    </div>
                </template>
            </swiping-card>
        </template>

        <div class="row">
            <div class="col">
                <div ref="loadingContainerOverride" style="height: 100px"></div>
            </div>
        </div>

        <filters-modal ref="filtersModal">
            <template v-slot:actions="slotScopes">
                <button :disabled="true" type="button" @click.prevent="" class="col btn mb-1 btn-primary">Print Courier Label</button>
            </template>
        </filters-modal>

    </div>
</template>

    <script>
    import beep from '../mixins/beep';
    import loadingOverlay from '../mixins/loading-overlay';

    import PacklistEntry from './Packlist/PacklistEntry';
    import PackedEntry from './Packlist/PackedEntry';

    import OrderDetails from "./Packlist/OrderDetails";
    import BarcodeInputField from "./SharedComponents/BarcodeInputField";
    import FiltersModal from "./Packlist/FiltersModal";
    import url from "../mixins/url";
    import SetShippingNumberModal from "./Packlist/ShippingNumberModal";
    import api from "../mixins/api";
    import helpers from "../mixins/helpers";
    import Vue from "vue";
    import NumberCardCol from "./SharedComponents/NumberCardCol";
    import SwipingCard from "./SharedComponents/SwipingCard";

    export default {
            mixins: [loadingOverlay, beep, url, api, helpers, BarcodeInputField],

            components: {
                PacklistEntry,
                FiltersModal,
                BarcodeInputField,
                NumberCardCol,
                SwipingCard,
                OrderDetails,
                PackedEntry,
                SetShippingNumberModal,
            },

            data: function() {
                return {
                    data: [],
                    fixToBottom: false,
                    nextUrl: null,
                    page: 1,
                };
            },


            mounted() {
                if (! Vue.prototype.$currentUser['warehouse_id']) {
                    this.$snotify.error('You do not have warehouse assigned. Please contact administrator', {timeout: 50000});
                    return
                }

                window.onscroll = () => this.loadMoreWhenNeeded();

                this.setUrlParameter('warehouse_id', Vue.prototype.$currentUser['warehouse_id']);

                this.loadData();
            },

            computed: {
                productUrl() {
                    return '/products?sku=' + this.productSku;
                },
            },

            methods: {
                loadMoreWhenNeeded() {
                    if (this.loading) {
                        return;
                    }

                    if (this.isMoreThanPercentageScrolled(70) === false) {
                        return;
                    }

                    if (! this.nextUrl) {
                        return;
                    }

                    this.loadData(++this.page);
                },

                loadData(page = 1) {
                    this.showLoading();

                    const params = {
                        'include': 'product',
                        'sort': '-id',
                        'per_page': 10,
                        'page': page,
                    }

                    this.apiGetDataCollectionRecord(params)
                        .then((response) => {
                            if (page === 1) {
                                this.data = response.data.data;
                            } else {
                                this.data = this.data.concat(response.data.data);
                            }
                            this.page = response.data.meta.current_page;
                            this.fixToBottom = false;
                            this.allLoaded = response.data.links.next === null;
                            this.nextUrl = response.data.links.next;
                        })
                        .catch((error) => {
                            this.displayApiCallError(error);
                        })
                        .finally(() => {
                            this.hideLoading();
                        });
                },

                onProductCountRequestResponse(response) {
                    const payload = {
                        'product_id': response['product_id'],
                        'quantity_collected': response['quantity'],
                    }

                    this.apiPostDataCollection(payload)
                        .then((response) => {
                            this.notifySuccess('Data collected');
                        })
                        .catch(e => {
                            this.displayApiCallError(e);

                            this.data.splice(this.data.indexOf(response), 1)
                        })
                        .finally(() => {
                            this.loadData();
                        });
                },
            },
    }
    </script>


<style lang="scss">

</style>
