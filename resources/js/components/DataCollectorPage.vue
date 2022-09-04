<template>
    <div>
        <div class="row mb-3 pl-1 pr-1 bg-white">
            <div class="flex-fill">
                <product-count-request-input-field @quantityRequestResponse="onProductCountRequestResponse" placeholder="Scan sku or alias"></product-count-request-input-field>
            </div>

            <barcode-input-field :url_param_name="'filter[shelf_location_greater_than]'" @barcodeScanned="setMinShelfLocation" placeholder="shelf" style="width: 75px" class="text-center ml-2 font-weight-bold"></barcode-input-field>

            <button v-b-modal="'configuration-modal'" type="button" class="btn btn-primary ml-2"><font-awesome-icon icon="cog" class="fa-lg"></font-awesome-icon></button>
        </div>


        <b-modal id="configuration-modal" centered no-fade hide-footer title="Data Collection">
            <a :href="getDownloadLink"  @click.prevent="downloadFileAndHideModal" v-b-toggle class="col btn mb-1 btn-primary">Download</a>
            <hr>
            <vue-csv-import
                v-model="csv"
                headers
                autoMatchFields
                loadBtnText="Load"
                :map-fields="['product_sku', 'quantity_requested']">

                <template slot="hasHeaders" slot-scope="{headers, toggle}">
                    <label>
                        <input type="checkbox" id="hasHeaders" :value="headers" @change="toggle">
                        Headers?
                    </label>
                </template>

                <template slot="error">
                    File type is invalid
                </template>

                <template slot="thead">
                    <tr>
                        <th>My Fields</th>
                        <th>Column</th>
                    </tr>
                </template>

                <!--            <template slot="next" slot-scope="{load}">-->
                <!--                <button @click.prevent="load"></button>-->
                <!--            </template>-->

                <template slot="submit" slot-scope="{submit}">
                    <button @click.prevent="submit">send!</button>
                </template>
            </vue-csv-import>

            <button v-if="csv" type="button" @click.prevent="postCsvRecordsToApiAndCloseModal" class="col btn mb-1 btn-primary">Import Records</button>

        </b-modal>

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

        <div class="row">
            <div class="col">
                <div ref="loadingContainerOverride" style="height: 50px"></div>
            </div>
        </div>

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
    import { VueCsvImport } from 'vue-csv-import';

    export default {
            mixins: [loadingOverlay, beep, url, api, helpers],

            components: {
                FiltersModal,
                NumberCard,
                SwipingCard,
                VueCsvImport
            },

            props: {
                data_collection_id: null,
            },

            data: function() {
                return {
                    data: [],
                    nextUrl: null,
                    page: 1,
                    per_page: 10,
                    csv: null,
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

                    // we double per_page every second page load to avoid hitting the API too hard
                    // and we will limit it to 100-ish per_page
                    if ((this.page % 2 === 0) && (this.per_page < 100)) {
                        this.page = this.page/ 2;
                        this.per_page = this.per_page * 2;
                    }

                    this.loadData(++this.page);
                },

                setMinShelfLocation (shelfLocation) {
                    this.setUrlParameter( "filter[shelf_location_greater_than]", shelfLocation);
                    this.loadData();
                },

                loadData(page = 1) {
                    this.showLoading();

                    const params = this.$router.currentRoute.query;
                    params['filter[data_collection_id]'] = this.data_collection_id;
                    params['per_page'] = this.per_page;
                    params['page'] = page;

                    this.apiGetDataCollectorRecords(params)
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
                        'data_collection_id': this.data_collection_id,
                        'product_id': response['product_id'],
                        'quantity_scanned': response['quantity'],
                    }

                    this.apiPostDataCollectorRecords(payload)
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

                postCsvRecordsToApiAndCloseModal() {
                    const data = this.csv.map(record => ({
                        'product_sku': record.product_sku,
                        'quantity_requested': record.quantity_requested,
                    }));

                    //we removing header row from csv
                    data.shift();

                    const payload = {
                        'data_collection_id': this.data_collection_id,
                        'data': data,
                    }

                    this.apiPostCsvImport(payload)
                        .then(() => {
                            this.notifySuccess('Records imported');
                            this.$bvModal.hide('configuration-modal');
                        })
                        .catch(e => {
                            this.displayApiCallError(e);
                        })
                        .finally(() => {
                            this.loadData();
                        });
                },

                downloadFileAndHideModal($event) {
                    let routeData = this.$router.resolve({
                        path: this.$router.currentRoute.fullPath,
                        query: {filename: "test.csv"}
                    });

                    window.location = routeData.href;

                    this.$bvModal.hide('configuration-modal');
                },
            },
        computed: {
            getDownloadLink() {
                let routeData = this.$router.resolve({
                    path: this.$router.currentRoute.fullPath,
                    query: {filename: "test.csv"}
                });

                return routeData.href;
            },
        },
    }
    </script>


<style lang="scss">

</style>
