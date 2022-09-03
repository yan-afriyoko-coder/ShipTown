<template>
    <div>
        <div class="row mb-3 pl-1 pr-1 bg-white">
            <div class="flex-fill">
                <product-count-request-input-field @quantityRequestResponse="onProductCountRequestResponse" placeholder="Scan sku or alias"></product-count-request-input-field>
            </div>

            <button v-b-modal="'new-collection-modal'" type="button" class="btn btn-primary ml-2"><font-awesome-icon icon="plus" class="fa-lg"></font-awesome-icon></button>
            <button disabled v-b-modal="'configuration-modal'" type="button" class="btn btn-primary ml-2"><font-awesome-icon icon="cog" class="fa-lg"></font-awesome-icon></button>
        </div>

        <b-modal id="new-collection-modal" centered no-fade hide-footer title="New Dats Collection">
            <barcode-input-field @barcodeScanned="createCollctionAndRedirect" placeholder="New Collection name"></barcode-input-field>
        </b-modal>

        <b-modal id="configuration-modal" autofocus centered no-fade hide-footer title="Data Collection">
            <button type="button" @click.prevent="downloadFile" class="col btn mb-1 btn-primary">Download</button>
        </b-modal>

        <template v-for="record in data">
            <swiping-card :disable-swipe-right="true" :disable-swipe-left="true">
                <template v-slot:content>
                    <a class="card-block stretched-link text-decoration-none" :href=" '/data-collector/' + record['id']">
                        <div class="row setting-list">
                            <div class="col-sm-12 col-lg-6">
                                <div class="text-primary h5">{{ record['name'] }}</div>
                                <div class="small text-secondary">{{ record['created_at'] |  moment('YYYY MMM DD H:mm') }}</div>
                            </div>
                        </div>
                    </a>
                </template>
            </swiping-card>
        </template>

        <div class="row"><div class="col">
                <div ref="loadingContainerOverride" style="height: 50px"></div>
        </div></div>

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
                createCollctionAndRedirect(name) {
                    const payload = {
                        'name': name,
                    }

                    this.apiPostDataCollection(payload)
                        .then(() => {
                            this.notifySuccess('Data collected');
                            this.$bvModal.hide('new-collection-modal');
                        })
                        .catch(e => {
                            this.displayApiCallError(e);
                        })
                        .finally(() => {
                            this.loadData();
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

                    this.loadData(++this.page);
                },

                loadData(page = 1) {
                    this.showLoading();

                    const params = this.$router.currentRoute.query;
                    params['sort'] = this.getUrlParameter('sort', '-created_at');
                    params['page'] = page;

                    this.apiGetDataCollectorList(params)
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

                downloadFile() {
                    let routeData = this.$router.resolve({
                        path: this.$router.currentRoute.fullPath,
                        query: {filename: "test.csv"}
                    });
                    window.open(routeData.href, '_blank');
                },
            },
    }
    </script>


<style lang="scss">
.setting-list:hover, .setting-list:focus {
    color: #495057;
    text-decoration: none;
    background-color: #f8f9fa;
}
</style>
