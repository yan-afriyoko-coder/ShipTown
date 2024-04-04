<template>
    <b-modal body-class="ml-0 mr-0 pl-1 pr-1" id="product-modal" size="xl" scrollable no-fade hide-header>
        <product-card v-if="product" :product="product" :expanded="true" :for-modal="true"/>
        <template #modal-footer>
            <b-button variant="secondary" class="float-right" @click="$bvModal.hide('product-modal');">
                Cancel
            </b-button>
            <b-button variant="primary" class="float-right" @click="$bvModal.hide('product-modal');">
                OK
            </b-button>
        </template>
    </b-modal>
</template>

<script>

import ProductCard from "./ProductCard.vue";
import api from "../../mixins/api.vue";

export default {
    components: {ProductCard},
    mixins: [api],

    created() {
        this.$eventBus.$on('show-product-modal', this.showProductModal);
    },

    data() {
        return {
            product: null
        }
    },

    methods: {
        showProductModal(sku) {
            this.$bvModal.show('product-modal');
            let params = {
                'filter[sku]': sku,
                'include': 'inventory,tags,prices,aliases,inventory.warehouse,inventoryMovementsStatistics',
            };
            this.apiGetProducts(params).then(response => {
                this.product = response.data.data[0];
            });
        },
    }
};

</script>
