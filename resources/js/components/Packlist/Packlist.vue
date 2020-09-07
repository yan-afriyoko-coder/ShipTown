<template>
    <div>

        <set-shipping-number-modal ref="shippingNumberModal" @shippingNumberUpdated="packAndShip"></set-shipping-number-modal>

        <filters-modal ref="filtersModal" @btnSaveClicked="onConfigChange">
            <template v-slot:actions="slotScopes">
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
                <div class="col-1">
                    <a style="cursor:pointer;" data-toggle="modal" data-target="#filterConfigurationModal">
                        <font-awesome-icon icon="cog"></font-awesome-icon>
                    </a>
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
                    this.printAddressLabel();
                    $(shippingNumberModal).modal('show');
                }
            }
        },

        mounted() {
            this.loadOrder()
                .then(() => {
                    // this.loadPacklist();
                });
        },

        methods: {
            packAndShip(shipping_number) {
                return  axios.put('api/orders/' + this.order['id'], {
                        'shipping_number': shipping_number,
                        'is_packed': true,
                    })
                    .then(() => {
                        this.loadOrder();
                    });
            },

            markAsPacked: function () {
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
                        if(data.total === 0) {
                            this.hideLoading();
                            return;
                        }

                        this.order = data.data[0];
                        this.hideLoading();
                    })
                    .catch( error => {
                        this.$snotify.error('Error occurred while loading packlist');
                        this.hideLoading();
                    })


            },

            loadPacklist: function() {

                if(!this.isLoading) {
                    this.showLoading();
                }

                // this.packlist = [];
                this.packed = [];

                axios.get('/api/order/products', {
                    params: {
                        'filter[order_id]': this.order.id,
                        'include': 'product,product.aliases',
                        'per_page': 999,
                    }})

                    .then(({ data }) => {

                        if(data.total === 0) {
                            this.hideLoading();
                            return;
                        }

                        this.packlist = data.data;

                        this.hideLoading();
                    })
                    .catch( error => {
                        this.$snotify.error('Error occurred while loading packlist');
                        this.hideLoading();
                    })


            },

            loadPacklistOld: function() {

                if(!this.isLoading) {
                    this.showLoading();
                }

                this.packlist = [];
                this.packed = [];

                axios.get('/api/packlist', {
                    params: {
                        'filter[order_id]': this.order.id,
                        'include': 'product,product.aliases',
                    }})
                    .then(({ data }) => {

                        if(data.total === 0) {
                            this.hideLoading();
                            return;
                        }

                        data.data.forEach(element => {
                            if(element.is_packed === true) {
                                this.packed.push(element);
                            } else {
                                this.packlist.push(element);
                            }
                        });

                        this.hideLoading();
                    })
                    .catch( error => {
                        this.$snotify.error('Error occurred while loading packlist');
                        this.hideLoading();
                    })


            },

            resetPick(pickedItem) {
                pickedItem.is_packed = !pickedItem.is_packed;
                pickedItem.quantity_packed = 0;
                this.packed.splice(this.packed.indexOf(pickedItem), 1);
                this.packlist.unshift(pickedItem);
                this.warningBeep();
            },

            skipPick(pickedItem) {
                // for visual effect we remove it straight away from UI
                // we will add it back in catch
                this.packlist.splice(this.packlist.indexOf(pickedItem), 1);

                return this.updatePick(pickedItem.id, 0, true)
                    .then( response => {
                        pickedItem.is_packed = !pickedItem.is_packed;
                        this.packed.unshift(pickedItem);
                        pickedItem.quantity_packed = 0;
                        this.displayWarningNotification(pickedItem, 'Skipped');
                        this.warningBeep();
                    })
                    .catch( error  => {
                        this.packlist.unshift(pickedItem);
                        this.$snotify.error('Not skipped (Error '+ error.response.status+')');
                        this.errorBeep();
                    });
            },

            pickAll(pickedItem) {
                this.packlist.splice(this.packlist.indexOf(pickedItem), 1);
                this.packed.unshift(pickedItem);
                this.beep();
            }

            ,pickAllOld(pickedItem) {
                // for visual effect we remove it straight away from UI
                // we will add it back in catch
                this.packlist.splice(this.packlist.indexOf(pickedItem), 1);

                return this.updatePick(pickedItem.id, pickedItem.quantity_requested, true)
                    .then( response => {
                        pickedItem.is_packed = true;
                        this.packed.unshift(pickedItem);
                        pickedItem.quantity_packed = pickedItem.quantity_requested;
                        this.displayPickedNotification(pickedItem, pickedItem.quantity_requested);
                        this.beep();
                    })
                    .catch( error  => {
                        pickedItem.is_packed = false;
                        this.packlist.unshift(pickedItem,0,pickedItem);
                        this.packed.splice(this.packed.indexOf(pickedItem), 1);
                        this.$snotify.error('Items not picked (Error '+ error.response.status+')');
                        this.errorBeep();
                    });
            },

            updatePick(pickId, quantityPicked, isPicked) {
                return axios.post(`/api/packlist/${pickId}`, {
                    'quantity_packed': quantityPicked,
                    'is_packed': isPicked
                });
            },

            displayPickedNotification: function (pickedItem, quantity) {
                const msg =  quantity + ' x ' + pickedItem.sku_ordered + ' picked';
                this.$snotify.confirm(msg, {
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

                this.$snotify.error(`"${barcode}" not found on packlist!`, {
                    timeout: 1500,
                    showProgressBar: false,
                    icon: false,
                });
                this.errorBeep();
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
                        this.$snotify.success('Printing address label');
                    }).catch((error) => {
                        let errorMsg = 'Error occurred while sending print job';

                        if (error.response.status === 404) {
                            errorMsg = `Order #${orderNumber} not found.`;
                        }

                        this.$snotify.error(errorMsg);
                    });
            }
        },


    }
</script>

<style lang="scss">

</style>
