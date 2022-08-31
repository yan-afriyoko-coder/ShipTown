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
                            <div class="text-primary h5">{{ record['product_name'] }}</div>
                            <div>
                                product: <b><a target="_blank" :href="'/products?sku=' + record['product_sku']">{{ record['product_sku'] }}</a></b>
                            </div>
                        </div>

                        <div class="row-cols col-sm-12 col-lg-6 text-right">
                            <number-card label="requested" :number="record['quantity_requested']" v-if="record['quantity_requested']"></number-card>
                            <number-card label="scanned" :number="record['quantity_scanned']" v-bind:class="{ 'bg-warning': record['quantity_requested'] && record['quantity_scanned'] > record['quantity_requested'] }"></number-card>
                            <number-card label="to scan" :number="record['quantity_to_scan']" v-if="record['quantity_requested']"></number-card>
                            <text-card label="shelf" :text="record['shelf_location']"></text-card>
                        </div>
                    </div>
                </template>
            </swiping-card>
        </template>

        <div class="row"><div class="col">
                <div ref="loadingContainerOverride" style="height: 50px"></div>
        </div></div>

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

    import FiltersModal from "./Packlist/FiltersModal";
    import url from "../mixins/url";
    import api from "../mixins/api";
    import helpers from "../mixins/helpers";
    import Vue from "vue";
    import NumberCard from "./SharedComponents/NumberCard";
    import SwipingCard from "./SharedComponents/SwipingCard";

    export default {
            mixins: [loadingOverlay, beep, url, api, helpers],

            components: {
                FiltersModal,
                NumberCard,
                SwipingCard,
            },

            data: function() {
                return {
                    data: [],
                    nextUrl: null,
                    page: 1,
                };
            },

            mounted() {
                if (! Vue.prototype.$currentUser['warehouse_id']) {
                    this.$snotify.error('You do not have warehouse assigned. Please contact administrator', {timeout: 50000});
                    return;
                }
                this.setUrlParameter('warehouse_id', Vue.prototype.$currentUser['warehouse_id']);

                window.onscroll = () => this.loadMoreWhenNeeded();

                this.loadData();
            },

            methods: {
                loadMoreWhenNeeded() {
                    if (this.isLoading) {
                        return;
                    }

                    if (this.isMoreThanPercentageScrolled(70) === false) {
                        return;
                    }

                    if (this.nextUrl === null) {
                        return;
                    }

                    this.loadData(++this.page);
                },

                loadData(page = 1) {
                    this.showLoading();

                    const params = this.$router.currentRoute.query;
                    params['page'] = page;

                    this.apiGetDataCollectionRecord(params)
                        .then((response) => {
                            if (page === 1) {
                                this.data = response.data.data;
                            } else {
                                this.data = this.data.concat(response.data.data);
                            }
                            this.page = response.data['meta']['current_page'];
                            this.nextUrl = response.data['links']['next'];
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
                        'quantity_scanned': response['quantity'],
                    }

                    this.apiPostDataCollection(payload)
                        .then(() => {
                            this.notifySuccess('Data collected');
                        })
                        .catch(e => {
                            this.displayApiCallError(e);
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
