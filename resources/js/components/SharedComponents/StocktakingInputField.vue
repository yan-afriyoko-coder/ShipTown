<template>
    <div>
        <barcode-input-field placeholder="Scan sku or alias to stocktake product"
                             @barcodeScanned="barcodeScanned"
        />

        <b-modal @ok="submitStocktake" id="quantity-request-modal" scrollable centered no-fade hide-header>
            <template v-if="inventory">
                <div>SKU: {{ inventory.product.sku }}</div>
                <div>Name: {{ inventory.product.name }}</div>
                <div>Stock: {{ inventory.quantity }}</div>
                <div class="row mt-2">
                    <div class="col-6">
                        <label class="small" for="adjust-by-request-input">adjust by</label>
                        <input class="form-control " :placeholder="'Adjust by'" :class="{ 'border-danger': this.adjustByQuantity < 0, 'border-success': this.adjustByQuantity > 0}"
                               id="adjust-by-request-input"
                               dusk="adjust-by-request-input"
                               v-model="adjustByQuantity"
                               type="number"
                               inputmode="numeric"
                               @keyup.enter="submitStocktake"
                        />
                    </div>
                    <div class="col-6">
                        <label class="small" for="adjust-by-request-input">new quantity</label>
                        <input class="form-control" :placeholder="'New quantity'" :class="{ 'border-danger': this.adjustByQuantity < 0, 'border-success': this.adjustByQuantity > 0}"
                               id="quantity-request-input"
                               dusk="quantity-request-input"
                               v-model="newQuantity"
                               type="number"
                               inputmode="numeric"
                               @keyup.enter="submitStocktake"
                        />
                    </div>
                </div>
                <input class="form-control mt-2" :placeholder="'Reason'" disabled
                       id="reason-request-input"
                       dusk="reason-request-input"
                       v-model="description"
                       type="text"
                       @keyup.enter="submitStocktake"
                />
            </template>
        </b-modal>

    </div>
</template>

<script>
    import loadingOverlay from '../../mixins/loading-overlay';
    import BarcodeInputField from "./../SharedComponents/BarcodeInputField";
    import api from "../../mixins/api";
    import helpers from "../../mixins/helpers";
    import url from "../../mixins/url";

    export default {
        mixins: [loadingOverlay, url, api, helpers],

        components: {
            BarcodeInputField
        },

        data: function() {
            return {
                inventory: null,
                adjustByQuantity: null,
                newQuantity: null,
                description: 'stocktake',
                recentStocktakes: [],
                stocktakeSuggestions: [],
            };
        },

        watch: {
            adjustByQuantity() {
                let adjustByValue = 0;
                let newValue = null;

                if (this.adjustByQuantity === null) {
                    if (this.newQuantity === '') {
                        return;
                    }
                    if (this.newQuantity === null) {
                        return;
                    }
                } else if (this.adjustByQuantity === '') {
                    if (this.newQuantity === '') {
                        return;
                    }
                } else {
                    adjustByValue = this.adjustByQuantity;
                }

                newValue = Number(this.inventory.quantity) + Number(adjustByValue);

                if (this.newQuantity !== newValue) {
                    this.newQuantity = newValue;
                }
            },

            newQuantity() {
                let newValue = 0;

                if (this.newQuantity === null) {
                    this.adjustByQuantity = null;
                    return;
                } else if (this.newQuantity === '') {
                    this.adjustByQuantity = null;
                    return;
                } else {
                    newValue = this.newQuantity - this.inventory.quantity;
                }

                if (this.adjustByQuantity !== newValue) {
                    this.adjustByQuantity = newValue === 0 ? '' : newValue;
                }
            }
        },

        mounted() {
            this.reloadData();

            if (! this.currentUser()['warehouse_id']) {
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
            reloadData() {},

            barcodeScanned: async function (barcode) {
                if (barcode === null) {
                    return;
                }

                if (barcode === "") {
                    return;
                }

                this.inventory = null;
                this.newQuantity = null;

                const params = {
                    'filter[sku_or_alias]': barcode,
                    'filter[warehouse_id]': this.currentUser()['warehouse_id'],
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
                        this.reloadData();
                    });
            },

            submitStocktake: function () {
                if (this.newQuantity === null) {
                    return;
                }

                if (this.newQuantity === "") {
                    return;
                }

                if (this.newQuantity < 0) {
                    this.notifyError('Minus quantity not allowed');
                    this.setFocusElementById(100, 'quantity-request-input', true, false)
                    return;
                }

                this.$bvModal.hide('quantity-request-modal');

                const delta_quantity = this.newQuantity - this.inventory.quantity;

                if (delta_quantity === 0) {
                    this.notifySuccess('Stock correct');
                    this.reloadData();
                    return;
                }

                const data = {
                    'product_id': this.inventory['product_id'],
                    'warehouse_id': this.currentUser()['warehouse_id'],
                    'description': this.description,
                    'quantity': delta_quantity,
                };

                this.apiPostInventoryMovement(data)
                    .then(() => {
                        this.notifySuccess('Inventory updated');
                    })
                    .catch((error) => {
                        this.displayApiCallError(error);
                    })
                    .finally(() => {
                        this.reloadData();
                    });
            },
        },
    }
</script>

<style lang="scss" scoped>
</style>
