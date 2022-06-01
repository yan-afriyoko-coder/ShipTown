<template>
    <div>
        <template v-if="getUrlParameter('hide_nav_bar', false) === false">
            <div class="row mb-3 pl-1 pr-1">
                <div class="flex-fill">
                    <barcode-input-field placeholder="Search products using name, sku, alias or command"
                                         ref="barcode"
                                         @barcodeScanned="test"
                    />
                </div>

                <button disabled type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#filterConfigurationModal"><font-awesome-icon icon="cog" class="fa-lg"></font-awesome-icon></button>
            </div>
        </template>


        <div class="row">
            <div class="col">
                <div ref="loadingContainerOverride" style="height: 100px"></div>
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
            };
        },

        mounted() {
        },

        methods: {
            test: function (e) {
                console.log(e);
            },

            findProduct: function (barcode) {
                const params = {
                    'filter[sku]': barcode,
                    'per_page': 1
                }

                this.apiGetProducts(params)
                    .then(e => {
                        if (e.data.meta.total > 0) {
                            this.$emit('productScanned', e.data)
                        }
                    });

                return false;
            },
        },
    }
</script>

<style lang="scss" scoped>
</style>
