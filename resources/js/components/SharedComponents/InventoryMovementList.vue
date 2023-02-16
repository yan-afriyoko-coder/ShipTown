<template>
    <div>
        <!-- <div class="row pl-2 p-0">
            <div class="col-6 text-left align-bottom pb-0 m-0 font-weight-bold text-uppercase small text-secondary">
                Inventory Movements
            </div>
            <div class="col-6 text-nowrap">
                <date-selector-widget :dates="{'url_param_name': 'filter[created_at_between]'}"></date-selector-widget>
            </div>
        </div> -->

        <template v-for="record in records">
            <swiping-card :disable-swipe-right="true" :disable-swipe-left="true"  :key="record.id">
                <template v-slot:content>
                    <inventory-movement-card :record="record" />
                </template>
            </swiping-card>
        </template>

        <template  v-if="isLoading === false && records !== null && records.length === 0" >
            <div class="row">
                <div class="col">
                    <div class="alert alert-info" role="alert">
                        No records found.
                    </div>
                </div>
            </div>
        </template>

        <div class="row">
            <div class="col">
                <div ref="loadingContainerOverride" style="height: 32px"></div>
            </div>
        </div>

    </div>
</template>

<script>

import loadingOverlay from '../../mixins/loading-overlay';
import url from "../../mixins/url";
import api from "../../mixins/api";
import helpers from "../../mixins/helpers";
import InventoryMovementCard from './InventoryMovementCard';

export default {
    components: { InventoryMovementCard },
    mixins: [loadingOverlay, url, api, helpers],

    props: {
        product_sku: {
            default: null,
            type: String
        }
    },

    data: function() {
        return {
            pagesLoaded: 0,
            reachedEnd: false,

            filter: {},
            records: null,
        };
    },

    mounted() {
        window.onscroll = () => this.loadMore();

        this.loadRecords(1);
    },

    methods: {
        reloadProducts() {
            this.products = [];
            this.loadRecords();
        },

        loadRecords: function(page = 1) {
            this.showLoading();

            let params = {
                filter: null,
                include: 'product,inventory,user,product.tags',
                sort: '-created_at',
                page, page
            };

            if (this.currentUser()['warehouse']) {
                params["filter[warehouse_code]"] = this.currentUser()['warehouse']['code'];
            }
            params["filter[search]"] = this.product_sku

            this.apiGetInventoryMovements(params)
                .then(({data}) => {
                    if (page === 1) {
                        this.records = [];
                    }

                    this.reachedEnd = data.data.length === 0;
                    this.pagesLoaded = page;

                    this.records = this.records.concat(data.data);
                })
                .finally(() => {
                    this.hideLoading();
                });
        },

        loadMore: function () {
            if (this.isLoading) {
                return;
            }

            if (this.reachedEnd) {
                return;
            }

            if (! this.isMoreThanPercentageScrolled(70)) {
                return;
            }

            this.loadRecords(++this.pagesLoaded);
        },

        hasMorePagesToLoad: function () {
            return this.lastPage > this.lastPageLoaded;
        },
    },
}
</script>

<style>

</style>
