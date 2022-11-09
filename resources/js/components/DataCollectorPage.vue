<template>
    <div>
        <swiping-card>
            <template v-slot:content>
                    <div class="row setting-list">
                        <div class="col-sm-12 col-lg-6">
                            <div class="text-primary h5">{{ dataCollection ? dataCollection['data']['0']['name'] : '' }}</div>
                            <div class="small text-secondary">{{ dataCollection ? dataCollection['data']['0']['created_at'] : ''  }}</div>
                        </div>
                    </div>
            </template>
        </swiping-card>

        <div class="row mb-1 pb-2 p-1 mt-0 sticky-top bg-light flex-nowrap" style="z-index: 10;">
            <div class="flex-fill">
                <product-count-request-input-field
                    @warehouseId="dataCollection['data']['0']['warehouse_id']"
                    @quantityRequestResponse="onProductCountRequestResponse"
                    requestedQuantity=""
                    placeholder="Scan sku or alias">
                </product-count-request-input-field>
            </div>

            <barcode-input-field :url_param_name="'filter[shelf_location_greater_than]'" @barcodeScanned="setMinShelfLocation" placeholder="shelf" style="width: 75px" class="text-center ml-2 font-weight-bold"></barcode-input-field>

            <button v-b-modal="'configuration-modal'" type="button" class="btn btn-primary ml-2"><font-awesome-icon icon="cog" class="fa-lg"></font-awesome-icon></button>
        </div>

        <template v-for="record in data">
            <swiping-card :disable-swipe-right="true" :disable-swipe-left="true">
                <template v-slot:content>
                    <div class="row" >
                        <div class="col-sm-12 col-lg-5">
                            <product-info-card :product= "record['product']"></product-info-card>
                        </div>

                        <div class="row col-sm-12 col-lg-7 text-right">
                            <div class="col-12 col-md-4 text-left small">
                               <div>in stock: <strong>{{ dashIfZero(Number(record['inventory_quantity'])) }}</strong></div>
                            </div>
                            <div class="col-12 col-md-8 text-right">
                                <number-card label="requested" :number="record['quantity_requested']" v-if="record['quantity_requested']"></number-card>
                                <number-card label="total out" :number="record['total_transferred_out']" v-if="record['total_transferred_out'] > 0"></number-card>
                                <number-card label="total in" :number="record['total_transferred_in']" v-if="record['total_transferred_in'] > 0" ></number-card>
                                <number-card label="scanned" :number="record['quantity_scanned']" v-bind:class="{'bg-warning': record['quantity_requested'] &&  record['quantity_requested'] < record['quantity_scanned'] + record['total_transferred_out'] + record['total_transferred_in']}"></number-card>
                                <number-card label="to scan" :number="record['quantity_to_scan']" v-if="record['quantity_requested']"></number-card>
                                <text-card label="shelf" :text="record['shelf_location']"></text-card>
                            </div>
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

        <b-modal id="configuration-modal" centered no-fade hide-footer hide-header
                 @shown="setFocusElementById(100,'stocktake-input', true, true)"
                 @hidden="setFocusElementById(100,'barcodeInput', true, true)"
        >
            <div v-if="dataCollection['data'][0]['deleted_at'] === null">
                <stocktake-input></stocktake-input>

                <div v-if="dataCollection['data'][0]['type'] === 'App\\Models\\DataCollectionTransferIn'">
                    <hr>
                    <button @click.prevent="receiveAll" v-b-toggle class="col btn mb-2 btn-primary">Receive ALL</button>
                    <button @click.prevent="transferStockIn" v-b-toggle class="col btn mb-2 btn-primary">Receive Scanned In</button>
                </div>

                <div v-if="dataCollection['data'][0]['type'] === null">
                    <hr>
                    <button @click.prevent="autoScanAll" v-b-toggle class="col btn mb-2 btn-primary">AutoScan ALL Records</button>
                    <hr>
                    <button @click.prevent="transferStockIn" v-b-toggle class="col btn mb-2 btn-primary">Transfer IN</button>
                    <button @click.prevent="transferStockOut" v-b-toggle class="col btn mb-2 btn-primary">Transfer OUT</button>
                    <button @click.prevent="transferToWarehouseClick" v-b-toggle class="col btn mb-2 btn-primary">Transfer To...</button>
                </div>
            </div>
            <hr>
            <a :href="getDownloadLink"  @click.prevent="downloadFileAndHideModal" v-b-toggle class="col btn mb-1 btn-primary">Download</a>
            <div v-if="dataCollection['data'][0]['deleted_at'] === null">
                <hr>
                <vue-csv-import
                    v-model="csv"
                    headers
                    canIgnore
                    autoMatchFields
                    loadBtnText="Load"
                    :map-fields="['product_sku', 'quantity_requested', 'quantity_scanned']">

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

                    <template slot="submit" slot-scope="{submit}">
                        <button @click.prevent="submit">send!</button>
                    </template>
                </vue-csv-import>

                <button v-if="csv" type="button" @click.prevent="postCsvRecordsToApiAndCloseModal" class="col btn mb-1 btn-primary">Import Records</button>

            </div>
        </b-modal>

        <b-modal id="transferToModal" centered no-fade hide-footer hide-header
                 @hidden="setFocusElementById(100,'barcodeInput', true, true)"
        >
            <template v-for="warehouse in warehouses">
                <button @click.prevent="transferToWarehouse(warehouse)" v-b-toggle class="col btn mb-2 btn-primary">{{ warehouse.name }}</button>
            </template>

        </b-modal>


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
                    scannedInQuantity: 1,
                    skuToStocktake: '',
                    dataCollection: null,
                    data: [],
                    nextUrl: null,
                    page: 1,
                    per_page: 10,
                    csv: null,
                    warehouses: [],
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

                this.apiGetWarehouses({'per_page': 999})
                    .then(response => {
                        this.warehouses = response.data.data;
                    });

                this.apiGetDataCollectorList({'filter[id]': this.data_collection_id})
                    .then(response => {
                        this.dataCollection = response.data;
                    });
            },

            methods: {
                transferToWarehouseClick() {
                    this.$bvModal.hide('configuration-modal');
                    this.$bvModal.show('transferToModal');
                },

                transferToWarehouse(warehouse) {
                    let data = {
                        'data_collector_id': this.data_collection_id,
                        'destination_warehouse_id': warehouse['id'],
                    }

                    this.apiDataCollectorActions('transfer-to-warehouse', data)
                        .then(response => {
                            this.$snotify.success('Transfer to warehouse initiated');
                            this.$bvModal.hide('transferToModal');
                            setTimeout(() => {
                                this.loadData();
                            }, 500);

                        })
                        .catch(error => {
                            this.showException(error);
                        });
                },

                transferStockOut() {
                    let data = {
                        'action': 'transfer_out_scanned',
                    }

                    this.apiUpdateDataCollection(this.data_collection_id, data)
                        .then(response => {
                            this.$snotify.success('Stock transferred out successfully');
                            this.$bvModal.hide('configuration-modal');
                            setTimeout(() => {
                                this.loadData();
                            }, 1000);
                        })
                        .catch(error => {
                            this.showException(error);
                        });
                },


                transferStockIn() {
                    let data = {
                        'action': 'transfer_in_scanned',
                    }

                    this.apiUpdateDataCollection(this.data_collection_id, data)
                        .then(response => {
                            this.$snotify.success('Stock transferred in successfully');
                            this.$bvModal.hide('configuration-modal');
                            setTimeout(() => {
                                this.loadData();
                            }, 500);
                        })
                        .catch(error => {
                            this.showException(error);
                        });
                },

                receiveAll() {
                    let data = {
                        'action': 'auto_scan_all_requested',
                    }

                    this.apiUpdateDataCollection(this.data_collection_id, data)
                        .then(response => {
                            this.transferStockIn();
                        })
                        .catch(error => {
                            this.showException(error);
                        });
                },

                autoScanAll() {
                    let data = {
                        'action': 'auto_scan_all_requested',
                    }

                    this.apiUpdateDataCollection(this.data_collection_id, data)
                        .then(response => {
                            this.$snotify.success('Auto scan completed successfully');
                            this.$bvModal.hide('configuration-modal');
                            setTimeout(() => {
                                this.loadData();
                            }, 500);
                        })
                        .catch(error => {
                            this.showException(error);
                        });
                },

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
                    params['include'] = 'product,inventory';
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
                        'quantity_scanned': record.quantity_scanned,
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
                        query: {
                            'select': 'product_sku,product_name,quantity_requested,quantity_to_scan,quantity_scanned',
                            'filter[data_collection_id]': this.data_collection_id,
                            filename: this.dataCollection['data']['0']['name'] +".csv"
                        }
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
