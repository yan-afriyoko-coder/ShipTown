<template>
    <b-modal body-class="ml-0 mr-0 pl-1 pr-1" id="recent-inventory-movements-modal" ref="blue" size="xl" scrollable no-fade>
        <template #modal-header>
            <span>Inventory Movements</span>
            <a :href="productItemMovementLink" class="fa-pull-right">See all</a>
        </template>

        <template v-for="record in records">
            <inventory-movement-card :record="record" />
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
            <b-button v-show="!isLoading" variant="outline-secondary" class="float-right" @click="$bvModal.hide('recent-inventory-movements-modal')">
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
        inventory_id: {
            default: null,
            type: Number
        },
    },

    watch: {
        inventory_id() {
            this.loadRecords()
        },
    },

    data: function() {
        return {
            records: [],
            hasNextPage: false
        };
    },

    computed: {
        productItemMovementLink() {
            return '/reports/inventory-movements?filter[inventory_id]=' + this.inventory_id;
        },
    },

    methods: {
        loadRecords: function(page = 1) {
            if (this.inventory_id == null) {
                this.notifyError('Inventory ID is required')
                return;
            }

            this.showLoading();
            this.records = []

            let params = {
                "filter[inventory_id]": this.inventory_id,
                include: 'product,inventory,user',
                sort: '-occurred_at,-sequence_number',
                page: page,
                per_page: 50
            };

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
