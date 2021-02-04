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

        <div v-if="!isLoading">
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

            <div v-if="packlist && packlist.length === 0 && packed.length === 0" class="row" >
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

        <set-shipping-number-modal ref="shippingNumberModal" @shippingNumberUpdated="addShippingNumber"></set-shipping-number-modal>

        <filters-modal ref="filtersModal">
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
        import api from "../mixins/api";
        import helpers from "../mixins/helpers";

        export default {
            mixins: [loadingOverlay, beep, url, api, helpers],

            components: {
                PacklistEntry,
                FiltersModal,
                BarcodeInputField,
                OrderDetails,
                PackedEntry,
                SetShippingNumberModal,
            },

            props: {
                order_number: null,
            },

            data: function() {
                return {
                    user: null,
                    order: null,
                    packlist: null,
                    packed: [],
                    somethingHasBeenPackedDuringThisSession: false,
                    autopilotEnabled: false,
                };
            },

            mounted() {
                this.loadUser();
                this.loadOrder(this.order_number);
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

                    if(this.packlist && this.packlist.length === 0) {
                        this.order['is_packed'] = true;
                        this.markAsPacked();
                        this.printAddressLabel();
                        this.displayShippingNumberModal();
                    }
                }
            },

            methods: {
                loadUser() {
                    this.apiGetUserMe()
                        .then(({data}) => {
                            this.user = (data.data);
                        });
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

                    return this.apiGetOrders(params)
                        .then(({data}) => {
                                this.order = data.meta.total > 0 ? data.data[0] : null;
                        })
                        .catch((error) => {
                            this.notifyError('Error occurred while loading order');
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

                    this.apiGetOrderProducts('/api/order/products', params)
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
                            this.notifyError('Error occurred while loading packlist');

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
                            this.notifySuccess('Status changed')
                        })
                        .catch(() => {
                            this.notifyError('Error when changing status');
                        });
                },

                displayShippingNumberModal() {
                    this.$refs.filtersModal.hide();
                    $('#shippingNumberModal').modal();
                },

                addShippingNumber(shipping_number) {
                    axios.post('/api/order/shipments', {
                        'order_id': this.order['id'],
                        'shipping_number': shipping_number,
                    })
                        .then(() => {
                            this.notifySuccess('Shipping number saved');
                        })
                        .catch( error => {
                            this.notifyError('Error saving shipping number, try again');
                        })
                },

                markAsPacked: function () {
                    return  axios.put('/api/orders/' + this.order['id'], {
                        'is_packed': true,
                    })
                        .catch((error) => {
                            let errorMsg = 'Error'+error.response.message;
                            this.notifyError(errorMsg);
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
                            this.notifySuccess('ha');
                        })
                        .catch((error) => {
                            this.addToLists(orderProduct);
                            this.notifyError('Error occurred when saving quantity shipped');
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

                    this.notifyError(`"${barcode}" not found on packlist!`);
                },

                printAddressLabel: function() {
                    let orderNumber = this.order['order_number'];

                    this.$refs.filtersModal.hide();

                    axios.put(`/api/print/order/${orderNumber}/address_label`)
                        .catch((error) => {
                            let errorMsg = 'Error occurred when printing label';

                            if (error.response.status === 404) {
                                errorMsg = `Order #${orderNumber} not found.`;
                            }

                            this.notifyError(errorMsg);
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
