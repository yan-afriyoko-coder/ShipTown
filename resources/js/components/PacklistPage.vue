<template>
    <div>

        <set-shipping-number-modal ref="shippingNumberModal" @shippingNumberUpdated="addShippingNumber"></set-shipping-number-modal>

        <filters-modal ref="filtersModal" @btnSaveClicked="onConfigChange">
            <template v-slot:actions="slotScopes">
                <button type="button" class="btn btn-info" @click.prevent="changeStatus('partially_shipped')">partially_shipped</button>
                <button type="button" class="btn btn-info" @click.prevent="changeStatus('for_later')">for_later</button>
                <button type="button" class="btn btn-info" @click.prevent="changeStatus('missing_item')">missing_item</button>
                <button type="button" class="btn btn-info" @click.prevent="changeStatus('picking')">picking</button>
                <hr>
                <button type="button" class="btn btn-info" @click.prevent="displayShippingNumberModal">Add Shipping Number</button>
                <button type="button" class="btn btn-info" @click.prevent="printAddressLabel">Print Address Label</button>
            </template>
        </filters-modal>

        <div v-if="showMultipackerWarning" class="row" >
            <div class="col alert-danger">
                    This order is already opened by someone else. Be careful
            </div>
        </div>

        <div v-if="order === null && !isLoading" class="row" >
            <div class="col text-center mt-3">
                <button type="button"  class="btn-info" @click.prevent="startPacking">
                    Start AutoPilot Packing
                </button>
            </div>
        </div>

        <div v-if="order !== null && !isLoading">

            <div class="row mb-3">
                <div class="col">
                    <order-details :order="order" />
                </div>
            </div>

            <div v-if="order['is_packed']" class="row" >
                <div class="col">
                    <div class="alert alert-danger" role="alert">
                        Order already packed...
                    </div>
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
                        Loading product list ...
                    </div>
                </div>
            </div>

            <template v-else class="row">

                <template v-for="record in packlist">
                    <div class="row mb-3">
                        <div class="col">
                            <packlist-entry :picklistItem="record" :key="record.id" @swipeRight="shipAll" @swipeLeft="shipPartialSwiped"/>
                        </div>
                    </div>
                </template>

                <template v-for="record in packed">
                    <div class="row mb-3">
                        <div class="col">
                            <packed-entry :picklistItem="record" :key="record.id" @swipeLeft="shipPartialSwiped" />
                        </div>
                    </div>
                </template>

            </template>

        </div>

    </div>

</template>

<script>
    import beep from '../mixins/beep';
    import loadingOverlay from '../mixins/loading-overlay';

    import PacklistEntry from './Packlist/PacklistEntry';
    import PackedEntry from './Packlist/PackedEntry';

    import OrderDetails from "./Packlist/OrderDetails";
    import BarcodeInputField from "./SharedComponents/BarcodeInputField";
    import FiltersModal from "./Packlist/FiltersModal";
    import url from "../mixins/url";
    import SetShippingNumberModal from "./Packlist/ShippingNumberModal";

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
                placeholder: 'test',
                user: null,
                order: null,
                packlist: null,
                packed: [],
                modalTest: false,
                showMultipackerWarning: false,
            };
        },

        mounted() {
            this.loadUser()
                .then(() => {
                    const order_number = this.getUrlParameter('order_number', null);
                    console.log(order_number);
                    if( order_number != null) {
                        this.packOrder(order_number);
                    }
                });
        },

        watch: {
            order() {
                if(this.order === null) {
                    return;
                }

                this.loadProducts();
            },
            packlist() {
                if(this.order === null) {
                    return;
                }

                if(this.packlist.length === 0) {
                    this.order['is_packed'] = true;
                    this.markAsPacked();
                    this.printAddressLabel();
                    this.displayShippingNumberModal();
                }
            }
        },

        methods: {
            startPacking() {
                let params = {}
                if( this.getUrlParameter('order_number') != null) {
                   params = {
                       'filter[order_number]': this.getUrlParameter('order_number')
                   };
                } else {
                   params = {
                        'filter[status]': this.getUrlParameter('status','picking'),
                        'sort': this.getUrlParameter('sort'),
                        'per_page': 1,
                        'include':'order_products'+
                            ',order_products.product'+
                            ',order_products.product.aliases',
                    };
                }

                axios.get('/api/packlist/order', {params: params})
                    .then(({data}) => {
                        this.packOrder(data.data['order_number'])
                    })
                    .catch((error) => {
                        let msg = 'Error occurred loading order';
                        if (error.response.status === 404) {
                            msg = "No orders available with specified filters"
                        }
                        this.$snotify.error(msg);
                        this.errorBeep();
                    })
            },

            packOrder(orderNumber) {
                const params = {
                    'filter[order_number]': orderNumber
                };

                this.loadNextOrderToPack(params)
            },

            loadNextOrderToPack: function (params) {
                this.showLoading();

                this.order = null;
                this.packlist = [];
                this.packed = [];

                return axios.get('/api/orders', {params: params})
                    .then(({data}) => {
                        if (data.total > 0) {
                            this.order = data.data[0];

                            if ((this.order['packer_user_id'] !== null) && (this.order['packer_user_id'] !== this.user['id'])) {
                                this.showMultipackerWarning = true;
                            }

                            if ((this.order['is_packed'] === false) && (this.order['packer_user_id'] === null)) {
                                return axios.put('api/orders/' + this.order['id'], {
                                    'packer_user_id': this.user['id'],
                                });
                            }
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                        this.notificationError('Error occurred while loading order');
                    })
                    .then(() => {
                        this.hideLoading();
                    })
            },



            shipPartialSwiped(orderProduct) {
                this.$snotify.prompt('Partial shipment', {
                    placeholder: 'Enter quantity to ship:',
                    position: 'centerCenter',
                    icon: false,
                    buttons: [
                        {
                            text: 'Ship',
                            action: (toast) => {

                                const maxQuantityAllowed = orderProduct['quantity_ordered'] - orderProduct['quantity_shipped'];
                                const minQuantityAllowed = -1 * orderProduct['quantity_shipped'];

                                if (
                                    isNaN(toast.value)
                                    || (toast.value > Number(maxQuantityAllowed))
                                    || (toast.value < Number(minQuantityAllowed))
                                ) {
                                    toast.valid = false;
                                    return false;
                                }

                                this.$snotify.remove(toast.id);
                                this.setQuantityShipped(orderProduct, Number(orderProduct['quantity_shipped']) + Number(toast.value));
                            }
                        },
                        {
                            text: 'Cancel',
                            action: (toast) => {
                                this.$snotify.remove(toast.id)
                            }
                        },
                    ],
                });
            },

            changeStatus(code) {
                this.$refs.filtersModal.hide();
                return axios.put('/api/orders/' + this.order['id'], {
                        'status_code': code
                    })
                    .then(() => {
                        this.$snotify.success('Status changed')
                    })
                    .catch(() => {
                        this.$snotify.error('Error when changing status');
                    })
                    .finally(() => {
                        this.startPacking()
                    });
            },

            loadUser() {
                return axios.get('api/user/me')
                    .then(({data}) => {
                        this.user = (data.data);
                    })
            },

            displayShippingNumberModal() {
                this.$refs.filtersModal.hide();
                $('#shippingNumberModal').modal();
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
                            this.order = null;
                            this.loadOrder();
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
                        this.order = null;
                        this.loadOrder();
                    });
            },

            markAsPacked: function () {
                return  axios.put('api/orders/' + this.order['id'], {
                        'is_packed': true,
                    })
                    .catch((error) => {
                    let errorMsg = 'Error'+error.response.message;

                    // if (error.response.status === 404) {
                    //     errorMsg = `Order #${orderNumber} not found.`;
                    // }

                    this.notificationError(errorMsg);
                });
            },

            loadOrder: function() {
                let params = {
                    'filter[is_packed]'     : false,

                    'filter[status]'        : this.getUrlParameter('status','picking'),
                    'sort'                  : this.getUrlParameter('sort'),


                    'per_page': 1,
                    'include': 'order_products'+
                        ',order_products.product'+
                        ',order_products.product.aliases',
                };

                if( this.getUrlParameter('order_number', null) === null) {
                    params['filter[is_picked]']      = this.getUrlParameter('is_picked', true);
                    params['filter[packer_user_id]'] = this.user['id'];
                } else {
                    params['filter[order_number]']   = this.getUrlParameter('order_number', null);
                }


                this.loadNextOrderToPack(params)
                    .then(() => {
                        if (this.order === null) {
                            let updatedParams = {
                                'filter[is_packed]'     : false,
                                'filter[has_packer]'    : false,

                                'filter[status]'        : this.getUrlParameter('status','picking'),
                                'sort'                  : this.getUrlParameter('sort'),

                                'per_page': 1,
                                'include': 'order_products'+
                                    ',order_products.product'+
                                    ',order_products.product.aliases',
                            };

                            if( this.getUrlParameter('is_picked', null) != null) {
                                params['filter[is_picked]'] = this.getUrlParameter('is_picked');
                            }

                            if( this.getUrlParameter('order_number', null) != null) {
                                params['filter[order_number]'] = this.getUrlParameter('order_number');
                            }

                            this.loadNextOrderToPack(updatedParams);
                        }
                    })
            },

            loadProducts: function() {
                this.showLoading();

                // this.packlist = [];
                this.packed = [];

                const params = {
                    'filter[inventory_source_location_id]': this.getUrlParameter('inventory_source_location_id', null),
                    'filter[order_id]': this.order.id,
                    'sort': 'inventory_source_shelf_location,sku_ordered',
                    'include': 'product,product.aliases',
                    'per_page': 999,
                };

                axios.get('/api/order/products', {params: params})
                    .then(({ data }) => {
                        if(data.total > 0) {
                            console.log(data.data)
                            // this.packlist = data.data;

                            this.packed = data.data.filter(
                                orderProduct => Number(orderProduct['quantity_shipped']) >= Number(orderProduct['quantity_ordered'])
                            );

                            this.packlist = data.data.filter(
                                orderProduct => Number(orderProduct['quantity_shipped']) < Number(orderProduct['quantity_ordered'])
                            );
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

            addToLists: function (orderProduct) {
                if (Number(orderProduct['quantity_ordered']) > Number(orderProduct['quantity_shipped'])) {
                    this.packlist.unshift(orderProduct);
                } else {
                    this.packed.unshift(orderProduct);
                }
            },

            removeFromLists: function (orderProduct) {
                if (this.packlist.indexOf(orderProduct) > -1) {
                    this.packlist.splice(this.packlist.indexOf(orderProduct), 1);
                } else {
                    this.packed.splice(this.packed.indexOf(orderProduct), 1);
                }
            },

            setQuantityShipped(orderProduct, quantity) {
                this.removeFromLists(orderProduct);

                axios.put('/api/order/products/' + orderProduct['id'], {
                    'quantity_shipped': quantity
                })
                    .then(({data}) => {
                        this.addToLists(data.data);
                        this.beep();
                    })
                    .catch((error) => {
                        this.addToLists(orderProduct);
                        this.$snotify.error('Error occurred when saving quantity shipped')
                        this.errorBeep();
                    });
            },

            shipAll(pickedItem) {
                this.setQuantityShipped(pickedItem, pickedItem['quantity_ordered']);
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
                    this.shipAll(pickItem);
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
                        let errorMsg = 'Error occurred when printing label';

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
