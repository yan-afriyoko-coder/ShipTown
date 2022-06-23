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

        <div class="row" v-if="isLoading">
            <div class="col">
                <div ref="loadingContainerOverride" style="height: 100px"></div>
            </div>
        </div>

        <div class="row" >
            <div class="col">
                <table class="fullWidth w-100">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Name</th>
                            <th class="text-right">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="itemMovement in recentStocktakes.data">
                            <td>{{ itemMovement['product']['sku'] }}</td>
                            <td>{{ itemMovement['product']['name'] }}</td>
                            <td class="text-right">{{ itemMovement['quantity_after'] }}</td>
                        </tr>
                    </tbody>
                </table>
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
                       dusk="quantity-request-input"
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
            };
        },

        mounted() {
            this.loadRecentStocktakes();

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
                        this.loadRecentStocktakes();
                    });
            },

            submitStocktake: function () {
                if (this.quantity === null) {
                    return;
                }

                if (this.quantity === "") {
                    return;
                }

                if (this.quantity < 0) {
                    this.notifyError('Minus quantity not allowed');
                    this.setFocusElementById(100, 'quantity-request-input', true, false)
                    return;
                }

                this.$bvModal.hide('quantity-request-modal');

                const delta_quantity = this.quantity - this.inventory.quantity;

                if (delta_quantity === 0) {
                    this.notifySuccess('Stock correct');
                    this.loadRecentStocktakes();
                    return;
                }

                const data = {
                    'product_id': this.inventory['product_id'],
                    'warehouse_id': this.currentUser()['warehouse_id'],
                    'description': 'stocktake',
                    'quantity': delta_quantity,
                };

                this.apiPostInventoryMovement(data)
                    .then(() => {
                        this.notifySuccess('Inventory updated');
                        this.loadRecentStocktakes();
                    })
                    .catch((error) => {
                        this.displayApiCallError(error);
                        this.loadRecentStocktakes();
                    });
            },

            loadRecentStocktakes() {
                const params = {
                    'filter[description]': 'stocktake',
                    'include': 'product',
                    'sort': '-id'
                }

                this.apiGetInventoryMovements(params)
                    .then((response) => {
                        this.recentStocktakes = response.data;
                    })
                    .catch((error) => {
                        this.displayApiCallError(error);
                    });
            }
        },

    }
</script>

<style lang="scss" scoped>
</style>
