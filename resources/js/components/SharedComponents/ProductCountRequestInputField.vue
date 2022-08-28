<template>
    <div>
        <barcode-input-field placeholder="Scan sku or alias to stocktake product"
                             @barcodeScanned="barcodeScanned"
        />

        <b-modal @ok="submitStocktake" id="quantity-request-modal" scrollable centered no-fade hide-header>
            <template v-if="inventory">
                <div>Name: {{ inventory.product.name }}</div>
                <div class="small">sku: {{ inventory.product.sku }}</div>
                <div class="row mt-2">
                    <div class="col-12">
<!--                        <label class="small" for="quantity-request-input">Quantity</label>-->
                        <input class="form-control" :placeholder="'quantity'" :class="{ 'border-danger': this.quantity < 0, 'border-success': this.quantity > 0}"
                               id="quantity-request-input"
                               dusk="quantity-request-input"
                               v-model="quantity"
                               type="number"
                               inputmode="numeric"
                               @keyup.enter="submitStocktake"
                        />
                    </div>
                </div>
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
                quantity: null,
                recentStocktakes: [],
                stocktakeSuggestions: [],
            };
        },

        mounted() {
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
            barcodeScanned: async function (barcode) {
                if (barcode === null) {
                    return;
                }

                if (barcode === "") {
                    return;
                }

                this.inventory = null;
                this.quantity = null;

                const params = {
                    'filter[sku_or_alias]': barcode,
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
                if (this.quantity === null) {
                    return;
                }

                if (this.quantity === "") {
                    return;
                }

                const data = {
                    'product_sku': this.inventory['product']['sku'],
                    'product_name': this.inventory['product']['name'],
                    'product_id': this.inventory['product_id'],
                    'quantity':  Number(this.quantity),
                };

                this.$emit('quantityRequestResponse', data);
            },
        },
    }
</script>

<style lang="scss" scoped>
</style>
