<template>
    <div>
        <div class="row mb-3 pl-1 pr-1">
            <div class="flex-fill">
                <barcode-input-field placeholder="Search products using name, sku, alias or command"
                                     @barcodeScanned="barcodeScanned"
                />
            </div>

            <button id="config-button" disabled type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#filterConfigurationModal"><font-awesome-icon icon="cog" class="fa-lg"></font-awesome-icon></button>
        </div>

        <div class="row">
            <div class="col">
                <div ref="loadingContainerOverride" style="height: 100px"></div>
            </div>
        </div>

        <b-modal scrollable centered no-fade hide-header
                 id="quantity-request-modal"
                 @ok="submitStocktake"
        >
            <template v-if="inventory">
                <div>SKU: {{ inventory.product.sku }}</div>
                <div>Name: {{ inventory.product.name }}</div>
                <div>Stock: {{ inventory.quantity }}</div>
                <input class="form-control" :placeholder="'New quantity'"
                       id="quantity-request-input"
                       v-model="quantity"
                       type="number"
                       inputmode="numeric"
                       @keyup.enter="submitStocktake"
                />
            </template>
        </b-modal>

    </div>
</template>

<script>
    import loadingOverlay from '../mixins/loading-overlay';
    import BarcodeInputField from "./SharedComponents/BarcodeInputField";
    import api from "../mixins/api";
    import helpers from "../mixins/helpers";
    import url from "../mixins/url";
    import Vue from "vue";

    export default {
        mixins: [loadingOverlay, url, api, helpers],

        components: {
            BarcodeInputField
        },

        data: function() {
            return {
                inventory: null,
                quantity: null,
            };
        },

        mounted() {
            if (! Vue.prototype.$currentUser['warehouse_id']) {
                this.$snotify.error('You do not have warehouse assigned. Please contact administrator', {timeout: 50000});
                return
            }

            this.$root.$on('bv::modal::hidden', (bvEvent, modalId) => {
                this.setFocusElementById(300, 'barcodeInput', true, true)
            })

            this.$root.$on('bv::modal::show', (bvEvent, modalId) => {
                // we need to disable it otherwise b-modal might return focus on it too quickly
                // and on screen keyboard will stay visible
                document.getElementById('barcodeInput').readOnly = true;
            })

            this.$root.$on('bv::modal::shown', (bvEvent, modalId) => {
                this.setFocusElementById(100, 'quantity-request-input', true, false)
            })
        },

        methods: {
            barcodeScanned: async function (barcode) {
                this.inventory = null;
                this.quantity = null;

                const params = {
                    'filter[product.sku]': barcode,
                    'filter[warehouse_id]': Vue.prototype.$currentUser['warehouse_id'],
                    'include': 'product'
                }

                this.apiGetInventory(params)
                    .then(e => {
                        if (e.data.meta.total === 0) {
                            this.notifyError('Product not found - "' + barcode + '"');
                            return;
                        }

                        this.inventory = e.data.data[0];

                        this.$bvModal.show('quantity-request-modal');
                    })
                    .catch((error) => {
                        this.displayApiCallError(error);
                    });
            },

            submitStocktake: function () {
                this.$bvModal.hide('quantity-request-modal');

                const delta_quantity = this.quantity - this.inventory.quantity;

                if (delta_quantity === 0) {
                    this.notifySuccess('Stock correct');
                    return;
                }

                const data = {
                    'product_id': this.inventory['product_id'],
                    'warehouse_id': Vue.prototype.$currentUser['warehouse_id'],
                    'description': 'stocktake',
                    'quantity': delta_quantity,
                };

                this.apiPostInventoryMovement(data)
                    .then(() => {
                        this.notifySuccess('Inventory updated');
                    })
                    .catch((error) => {
                        this.displayApiCallError(error);
                    });
            },
        },
    }
</script>

<style lang="scss" scoped>
</style>
