<template>
    <div>
        <div class="row mb-3 pl-1 pr-1">
            <div class="flex-fill">
                <product-count-request-input-field @quantityRequestResponse="onProductCountRequestResponse"></product-count-request-input-field>
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
                    <tbody>
                        <tr v-for="record in data" class="align-text-top">
                            <td>
                                {{ record['product_name'] ? record['product_name'] : record['product']['name'] }}
                                <br>
                                <span class="small">sku: {{ record['product_sku'] ? record['product_sku'] : record['product']['sku'] }}</span>
                            </td>
                            <td class="text-right h5 align-middle   ">{{ record['quantity'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

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
                data: [],
            };
        },

        mounted() {
            this.loadData();

            if (! this.currentUser()['warehouse_id']) {
                this.$snotify.error('You do not have warehouse assigned. Please contact administrator', {timeout: 50000});
                return;
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
            onProductCountRequestResponse(response) {
                console.log(response);

                this.data = [response].concat(this.data);

                this.apiPostDataCollectorRecords(response)
                    .then(() => {
                        this.loadData();
                    })
                    .catch(e => {
                        this.displayApiCallError(e);

                        this.data.splice(this.data.indexOf(response), 1)
                    });
            },

            loadData() {
                const params = {
                    'include': 'product',
                    'sort': '-id',
                    'per_page': 99999,
                }

                this.apiGetDataCollectionRecord(params)
                    .then((response) => {
                        this.data = response.data.data;
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
