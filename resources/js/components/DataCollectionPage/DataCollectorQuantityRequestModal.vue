<template>
    <div>
        <b-modal @ok="submitStocktake" id="data-collector-quantity-request-modal" scrollable centered no-fade hide-header
                 @shown="modalShown"
                 @hidden="onHidden"
        >
                <div class="row">
                    <product-info-card :product="product"></product-info-card>
                </div>
                <div class="row col mt-3 mb-3 small">
                    <number-card label="price" :number="prices ? prices['price'] : 0" class="float-left"></number-card>
                </div>
                <div class="row-col text-right mt-3 mb-3">
                    <number-card label="requested" :number="dataCollectionRecord ? dataCollectionRecord['quantity_requested'] : 0"></number-card>
                    <number-card label="scanned" :number="dataCollectionRecord ? dataCollectionRecord['quantity_scanned'] : 0"></number-card>
                    <number-card label="to_scan" :number="dataCollectionRecord ? dataCollectionRecord['quantity_to_scan'] : 0"></number-card>
<!--                    <number-card label="in stock" :number="product['inventory'][this.currentUser()['warehouse']['code']]['quantity']"></number-card>-->
                </div>

            <div class="row">
                <div class="col-12">
                    <input class="form-control" :placeholder="'quantity to add'" :class="{ 'border-danger': this.quantity < 0, 'border-success': this.quantity > 0}"
                           id="data-collection-record-quantity-request-input"
                           ref="data-collection-record-quantity-request-input"
                           dusk="data-collection-record-quantity-request-input"
                           v-model="quantity"
                           type="number"
                           inputmode="numeric"
                           @keyup.enter="submitStocktake"
                    />
                </div>
            </div>
        </b-modal>

    </div>
</template>

<script>
    import loadingOverlay from '../../mixins/loading-overlay';
    import BarcodeInputField from "../SharedComponents/BarcodeInputField";
    import api from "../../mixins/api";
    import helpers from "../../mixins/helpers";
    import url from "../../mixins/url";

    export default {
        mixins: [loadingOverlay, url, api, helpers],

        components: {
            BarcodeInputField
        },

        props: {
            dataCollection: null,
            dataCollectionRecord: null,
            product: null,
            placeholder: '',
            requestedQuantity: null,
        },

        data: function() {
            return {
                quantity: null,
                prices: null,
            };
        },

        mounted() {
            this.$root.$on('barcodeScanned', function () {
                this.$bvModal.show('data-collector-quantity-request-modal');
            });
        },

        watch: {
            product: function (newProduct, oldDataCollectionRecord) {
                if (newProduct === null) {
                    this.prices = null;
                    return;
                }

                this.apiGetProducts({
                    'filter[id]': newProduct['id'],
                    'include': 'prices',
                }).then(response => {
                    this.prices = response.data.data[0]['prices'][this.currentUser()['warehouse']['code']];
                });
            }
        },

        methods: {
            onHidden() {
                this.prices = null;
                this.quantity = null;
                this.$emit('hidden');
            },

            modalShown() {
                this.setFocusElementById(100, 'data-collection-record-quantity-request-input', true, false);
            },

            barcodeScanned: async function (barcode) {
                if (barcode === null) {
                    return;
                }

                if (barcode === "") {
                    return;
                }

                this.quantity = null;

                const params = {
                    'filter[sku_or_alias]': barcode,
                    'include': 'inventory,prices',
                };

                this.apiGetProducts(params)
                    .then(response => {
                        if (response.data.data.length === 0) {
                            this.notifyError('No product found with barcode: ' + barcode);
                            return;
                        }
                        this.product = response.data.data[0];

                        this.$bvModal.show('data-collector-quantity-request-modal');
                    })
                    .catch((error) => {
                        this.displayApiCallError(error);
                    });
            },

            submitStocktake: function () {
                this.$bvModal.hide('data-collector-quantity-request-modal');

                if (this.quantity === null) {
                    return;
                }

                if (this.quantity === "") {
                    return;
                }

                console.log(this.dataCollection);

                const data = {
                    'data_collection_id': this.dataCollection['id'],
                    'product_id': this.product['id'],
                    'quantity_scanned':  Number(this.quantity),
                };

                this.apiPostDataCollectorRecords(data)
                    .then(response => {
                        this.notifySuccess('Data collection record added');
                        this.$emit('dataCollectionRecordAdded', response.data.data);
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
