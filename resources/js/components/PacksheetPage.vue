<template>
    <div>
        <div class="row" v-if="showMultipackerWarning" >
            <div class="col">
                <div class="alert alert-danger" role="alert">
                    This order is already opened by someone else. Be careful
                </div>
            </div>
        </div>

        <div v-if="order && order['is_packed']" class="row" >
            <div class="col">
                <div class="alert alert-danger" role="alert">
                    Order already packed...
                </div>
            </div>
        </div>

        <div v-if="order === null && !isLoading" class="row text-center mt-3" >
            <button type="button"  class="btn-info" @click.prevent="fetchFromAutoPilot">
                Start AutoPilot Packing
            </button>
        </div>

        <div v-if="order !== null && !isLoading">
            <div class="row mb-3">
                <order-details :order="order" />
            </div>

            <div class="row mb-3">
                <div class="col-11">
                    <barcode-input-field @barcodeScanned="packBarcode"/>
                </div>
                <div class="col-1">
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

        <div class="row">
            <div class="col">
                <div ref="loadingContainerOverride" style="height: 100px"></div>
            </div>
        </div>

        <set-shipping-number-modal ref="shippingNumberModal" @shippingNumberUpdated="addShippingNumber"></set-shipping-number-modal>

        <filters-modal ref="filtersModal" @btnSaveClicked="onConfigChange">
            <template v-slot:actions="slotScopes">
                <button type="button" class="btn btn-info" @click.prevent="changeStatus('partially_shipped')">partially_shipped</button>
                <button type="button" class="btn btn-info" @click.prevent="changeStatus('for_later')">for_later</button>
                <button type="button" class="btn btn-info" @click.prevent="changeStatus('missing_item')">missing_item</button>
                <button type="button" class="btn btn-info" @click.prevent="changeStatus('picking')">picking</button>
                <button type="button" class="btn btn-info" @click.prevent="changeStatus('packing_warehouse')">packing_warehouse</button>
                <button type="button" class="btn btn-info" @click.prevent="changeStatus('fabrics')">fabrics</button>
                <button type="button" class="btn btn-info" @click.prevent="changeStatus('ready')">ready</button>
                <button type="button" class="btn btn-info" @click.prevent="displayShippingNumberModal">Add Shipping Number</button>
                <button type="button" class="btn btn-info" @click.prevent="printAddressLabel">Print Address Label</button>
            </template>
        </filters-modal>

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
                    user: null,
                    order: null,
                    packlist: null,
                    packed: [],
                    modalTest: false,
                    startAt:  (new Date).getTime(),
                    endAt:  this.startAt + 1000 * 60 * 7,
                    somethingHasBeenPackedDuringThisSession: false,
                    autopilotEnabled: false,
                };
            },

            mounted() {
                this.loadUser()
                    .then(() => {
                        if (this.getUrlParameter('order_number') != null) {
                            this.loadOrder(this.getUrlParameter('order_number'));
                        }
                    });
            },

            watch: {
                order() {
                    if(this.order) {
                        this.loadProducts();
                    }
                },
                packlist() {
                    if(this.order === null) {
                        return;
                    }

                    if (this.somethingHasBeenPackedDuringThisSession === false) {
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
                resetTimer() {
                    this.startAt = (new Date).getTime();
                    this.endAt =  this.startAt + 1000 * 60 * 7;
                },

                fetchFromAutoPilot() {
                    let params = {}

                    this.autopilotEnabled = this.getUrlParameter('order_number') === null;

                    if( this.autopilotEnabled === false) {
                        if(this.order) {
                            //we have already loaded this order
                            return;
                        }
                        params = {'filter[order_number]': this.getUrlParameter('order_number')};
                    } else {
                        params = {
                            'filter[status]': this.getUrlParameter('status','picking'),
                            'filter[inventory_source_location_id]': this.getUrlParameter('inventory_source_location_id'),
                            'sort': this.getUrlParameter('sort', 'order_placed_at'),
                        };
                    }

                    axios.get('/api/packlist/order', {params: params})
                        .then(({data}) => {
                            this.loadOrder(data.data['order_number'])
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

                loadOrder: function (orderNumber) {
                    this.showLoading();

                    this.order = null;
                    this.packlist = [];
                    this.packed = [];
                    this.somethingHasBeenPackedDuringThisSession = false;

                    const params = {
                        'filter[order_number]': orderNumber,
                        'include': 'order_comments,order_comments.user',
                    };

                    return axios.get('/api/orders', {params: params})
                        .then(({data}) => {
                                this.resetTimer();
                                this.order = data.meta.total > 0 ? data.data[0] : null;
                        })
                        .catch((error) => {
                            this.notificationError('Error occurred while loading order');
                        })
                        .finally(() => {
                            this.hideLoading();
                        })
                },

                loadProducts: function() {
                    this.showLoading();

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
                            if(data.meta.total > 0) {
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
                        .finally(() => {
                            this.hideLoading();
                        });
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
                            this.fetchFromAutoPilot()
                        });
                },

                loadUser() {
                    return axios.get('/api/settings/user/me')
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
                    axios.post('/api/order/shipments', {
                        'order_id': this.order['id'],
                        'shipping_number': shipping_number,
                    })
                        .then(() => {
                            this.$snotify.success('Shipping number saved');
                            if(this.packlist.length === 0) {
                                if (this.autopilotEnabled) {
                                    this.order = null;
                                }
                                this.fetchFromAutoPilot();
                            }
                        })
                        .catch( error => {
                            this.notificationError('Error saving shipping number, try again');
                        })
                },

                packAndShip() {
                    return axios.put('/api/orders/' + this.order['id'], {
                        'is_packed': true,
                    })
                        .then(() => {
                            this.order = null
                            this.fetchFromAutoPilot();
                        });
                },

                markAsPacked: function () {
                    return  axios.put('/api/orders/' + this.order['id'], {
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
                    this.somethingHasBeenPackedDuringThisSession = true;
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
                    this.fetchFromAutoPilot();
                },

                getValueOrDefault: function (value, defaultValue){
                    return (value === undefined) || (value === null) ? defaultValue : value;
                },

                printAddressLabel: function() {
                    let orderNumber = this.order['order_number'];

                    this.$refs.filtersModal.hide();

                    axios.put(`/api/print/order/${orderNumber}/address_label`)
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

            computed: {
                showMultipackerWarning()  {
                    if (this.order === null) {
                        return false;
                    }

                    return (this.order['packer_user_id']  && this.order['packer_user_id'] !== this.user['id']);
                }
            }
        }
    </script>


<style lang="scss">

</style>
