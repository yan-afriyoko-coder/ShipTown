<template>
    <b-modal id="show-inventory-movements" size="lg" scrollable no-fade hide-header>

        <div class="d-flex justify-content-between mb-2">
            <h6>Inventory Movements</h6>
            <a v-if="hasNextPage" :href="productItemMovementLink">See more</a>
        </div>

        <template v-for="record in records">
            <swiping-card :disable-swipe-right="true" :disable-swipe-left="true"  :key="record.id">
                <template v-slot:content>
                    <inventory-movement-card :record="record" />
                </template>
            </swiping-card>
        </template>

        <div class="d-flex align-items-center justify-content-center" style="height:100px" v-if="!isLoading && !records.length">
            No records found
        </div>

        <div class="row" v-if="isLoading">
            <div class="col">
                <div ref="loadingContainerOverride" style="height: 100px"></div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <a v-if="hasNextPage" :href="productItemMovementLink" target="_blank">See more</a>
        </div>

        <template #modal-footer>
            <b-button
                v-show="!isLoading"
                variant="outline-secondary"
                class="float-right"
                @click="$bvModal.hide('show-inventory-movements')"
            >
                Close
            </b-button>
        </template>
    </b-modal>
</template>

<script>

import api from "../../mixins/api";
import loadingOverlay from '../../mixins/loading-overlay';
import InventoryMovementCard from './InventoryMovementCard';

export default {
    components: { InventoryMovementCard },

    mixins: [api, loadingOverlay],

    props: {
        product_sku: {
            default: null,
            type: String
        }
    },

    watch: {
        product_sku() {
            this.loadRecords()
        }
    },

    data: function() {
        return {
            records: [],
            hasNextPage: false
        };
    },

    computed: {
        productItemMovementLink() {
            return '/reports/inventory-movements?hide_nav_bar=true&filter[search]=' + this.product_sku;
        },
    },

    methods: {
        loadRecords: function(page = 1) {
            this.showLoading();
            this.records = []

            let params = {
                filter: null,
                include: 'product,inventory,user,product.tags',
                sort: '-created_at',
                page,
                per_page: 10
            };

            if (this.currentUser()['warehouse']) {
                params["filter[warehouse_code]"] = this.currentUser()['warehouse']['code'];
            }
            params["filter[search]"] = this.product_sku

            this.apiGetInventoryMovements(params)
                .then(({data}) => {
                    this.records = this.records.concat(data.data);
                    this.hasNextPage = data.links.next != null
                })
                .finally(() => {
                    this.hideLoading();
                });
        },
    }
}
</script>

<style>

</style>
