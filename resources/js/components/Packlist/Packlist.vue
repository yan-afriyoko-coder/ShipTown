<template>
    <div>

        <set-shipping-number-modal ref="shippingNumberModal" @shippingNumberUpdated="addShippingNumber"></set-shipping-number-modal>

        <filters-modal ref="filtersModal" @btnSaveClicked="onConfigChange">
            <template v-slot:actions="slotScopes">
                <button type="button" class="btn btn-info" @click.prevent="displayShippingNumberModal">
                    Add Shipping Number
                </button>
                <button type="button" class="btn btn-info" @click.prevent="printAddressLabel">
                    Print Address Label
                </button>
            </template>
        </filters-modal>

        <div v-if="order === null && !isLoading" class="row" >
            <div class="col">
                <div class="warning alert-warning" role="alert">
                    No orders ready for packing
                </div>
            </div>
        </div>

        <div v-if="order !== null && !isLoading">

            <div class="row mb-3">
                <div class="col">
                    <order-details :order="order" />
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-11">
                    <barcode-input-field @barcodeScanned="packBarcode"/>
                </div>
                <div class="col-1 ml-0 pl-0">
                    <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterConfigurationModal" href="#"><font-awesome-icon icon="cog" class="fa-lg"></font-awesome-icon></button>
                </div>
            </div>

            <div v-if="packlist.length === 0 && packed.length === 0" class="row" >
                <div class="col">
                    <div class="alert alert-info" role="alert">
                        No products found.
                    </div>
                </div>
            </div>

            <template v-else class="row">

                <template v-for="record in packlist">
                    <div class="row mb-3">
                        <div class="col">
                            <packlist-entry :picklistItem="record" :key="record.id"  @swipeRight="pickAll" />
                        </div>
                    </div>
                </template>

                <template v-for="record in packed">
                    <div class="row mb-3">
                        <div class="col">
                            <packed-entry :picklistItem="record" :key="record.id" @swipeLeft="resetPick" />
                        </div>
                    </div>
                </template>

            </template>

        </div>

    </div>

</template>

<script>
    import beep from '../../mixins/beep';
    import loadingOverlay from '../../mixins/loading-overlay';

    import PacklistEntry from './mixins/PacklistEntry';
    import PackedEntry from './mixins/PackedEntry';

    import OrderDetails from "./mixins/OrderDetails";
    import BarcodeInputField from "../SharedComponents/BarcodeInputField";
    import FiltersModal from "./mixins/FiltersModal";
    import url from "../../mixins/url";
    import SetShippingNumberModal from "./mixins/SetShippingNumberModal";

    export default {
        mixins: [loadingOverlay, beep, url],

        components: {
            PacklistEntry,
            FiltersModal,
            BarcodeInputField,
            OrderDetails,
            PackedEntry,
            SetShippingNumberModal,
        },

        data: function() {
            return {
                order: null,
                packlist: null,
                packed: [],
                modalTest: false,
            };
        },

        watch: {
            order() {
                if(this.order === null) {
                    return;
                }

                this.loadPacklist();
            },
            packlist() {
                if(this.order === null) {
                    return;
                }

                if(this.packlist.length === 0) {
                    this.displayShippingNumberModal();
                }
            }
        },

        mounted() {
            this.loadOrder();
        },

        methods: {
            displayShippingNumberModal() {
                this.$refs.filtersModal.hide();
                $(shippingNumberModal).modal('show');
            },

            notificationError: function (message) {
                this.$snotify.error(message, {timeout: 5000});
                this.errorBeep();
            },

            addShippingNumber(shipping_number) {
                axios.post('api/order/shipments', {
                        'order_id': this.order['id'],
                        'shipping_number': shipping_number,
                    })
                    .then(() => {
                        this.$snotify.success('Shipping number saved');
                        if(this.packlist.length === 0) {
                            this.packAndShip();
                        }
                    })
                    .catch( error => {
                        this.notificationError('Error saving shipping number, try again');
                    })
            },

            packAndShip() {
                return axios.put('api/orders/' + this.order['id'], {
                        'is_packed': true,
                    })
                    .then(() => {
                        this.loadOrder();
                    });
            },

            markAsPacked: function () {
                console.log('markAsPacked');
                return  axios.put('api/orders/' + this.order['id'], {
                        'is_packed': true,
                })
            },

            loadOrder: function() {

                const params = {
                    'filter[order_number]': this.getUrlParameter('order_number', null),
                    'filter[is_picked]': this.getUrlParameter('is_picked', null),
                    'filter[is_packed]': false,
                    'filter[status]': 'picking',
                    'per_page': 1,
                    'include': 'order_products,order_products.product,order_products.product.aliases',
                };

                this.showLoading();

                this.order = null;
                this.packlist = [];
                this.packed = [];

                return axios.get('/api/orders', {params: params})
                    .then(({ data }) => {
                        if(data.total > 0) {
                            this.order = data.data[0];
                        }
                    })
                    .catch( error => {
                        this.notificationError('Error occurred while loading order');
                    })
                    .then(() => {
                        this.hideLoading();
                    })


            },

            loadPacklist: function() {

                this.showLoading();

                // this.packlist = [];
                this.packed = [];

                const params = {
                    'filter[inventory_source_location_id]': this.getUrlParameter('inventory_source_location_id', null),
                    'filter[order_id]': this.order.id,
                    'include': 'product,product.aliases',
                    'per_page': 999,
                };

                axios.get('/api/order/products', {params: params})
                    .then(({ data }) => {
                        if(data.total > 0) {
                            this.packlist = data.data;
                        }
                    })
                    .catch( error => {
                        this.notificationError('Error occurred while loading packlist');

                    })
                    .then(() => {
                        this.hideLoading();
                    });
            },

            resetPick(pickedItem) {
                pickedItem.is_packed = !pickedItem.is_packed;
                pickedItem.quantity_packed = 0;
                this.packed.splice(this.packed.indexOf(pickedItem), 1);
                this.packlist.unshift(pickedItem);
                this.warningBeep();
            },

            pickAll(pickedItem) {
                this.packlist.splice(this.packlist.indexOf(pickedItem), 1);
                this.packed.unshift(pickedItem);
                this.beep();
            },

            displayPickedNotification: function (pickedItem, quantity) {
                const msg =  quantity + ' x ' + pickedItem.sku_ordered + ' picked';
                this.$snotify.confirm(msg, {
                    timeout: 5000,
                    pauseOnHover: true,
                    showProgressBar: false,
                    buttons: [
                        {
                            text: 'Undo',
                            action: (toast) => {
                                this.$snotify.remove(toast.id);
                                this.resetPick(pickedItem);
                            }
                        }
                    ]
                });
            },

            displayWarningNotification: function (pickedItem, msg) {
                this.$snotify.warning(msg, {
                    timeout: 5000,
                    pauseOnHover: true,
                    showProgressBar: false,
                    icon: false,
                    buttons: [
                        {
                            text: 'Undo',
                            action: (toast) => {
                                this.$snotify.remove(toast.id);
                                this.resetPick(pickedItem);
                            }
                        }
                    ]
                });
            },

            findEntry: function (barcode) {
                for (let element of this.packlist) {

                    if(element.sku_ordered === barcode) {
                        return element;
                    }

                    if(typeof element.product === 'undefined' ){
                        continue;
                    }

                    if(element.product === null ){
                        continue;
                    }

                    if(element.product.sku === barcode) {
                        return element;
                    }

                    if(typeof element.product.aliases === 'undefined') {
                        continue;
                    }

                    for(let alias of element.product.aliases) {
                        if(alias.alias === barcode){
                            return element;
                        }
                    }


                }
                return null;
            },

            packBarcode: function (barcode) {
                if(barcode === '') {
                    return;
                }

                let pickItem = this.findEntry(barcode);

                if(pickItem) {
                    this.pickAll(pickItem);
                    return;
                }

                this.notificationError(`"${barcode}" not found on packlist!`);
            },

            onConfigChange: function(config) {
                this.loadOrder();
            },

            getValueOrDefault: function (value, defaultValue){
                return (value === undefined) || (value === null) ? defaultValue : value;
            },

            printAddressLabel: function() {
                let orderNumber = this.order['order_number'];

                this.$refs.filtersModal.hide();

                axios.put(`api/print/order/${orderNumber}/address_label`)
                    .then(() => {
                        this.$snotify.success('Address label printed', {
                            timeout: 1000,
                            icon: false,
                        });
                    }).catch((error) => {
                        let errorMsg = 'Error occurred while sending print job';

                        if (error.response.status === 404) {
                            errorMsg = `Order #${orderNumber} not found.`;
                        }

                        this.notificationError(errorMsg);
                    });
            }
        },


    }
</script>

<style lang="scss">

</style>
