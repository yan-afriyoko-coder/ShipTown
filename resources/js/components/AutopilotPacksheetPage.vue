<template>
    <div v-if="order">
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

            <template v-for="order_comment in order['order_comments']">
                <div class="row mb-2">
                    <div class="col">
                        <b>{{ order_comment['user']['name'] }}: </b>{{ order_comment['comment'] }}
                    </div>
                </div>
            </template>
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
                <button type="button" class="btn btn-info" @click.prevent="changeStatus('layaway')">layaway</button>
                <button type="button" class="btn btn-info" @click.prevent="changeStatus('complete')">complete</button>
                <button type="button" class="btn btn-info" @click.prevent="openPreviousOrder">Open Previous Order</button>
                <button type="button" class="btn btn-info" @click.prevent="askForShippingNumber">Add Shipping Number</button>
                <button type="button" class="btn btn-info" @click.prevent="printLabel('address_label')">Print Address Label</button>
                <button type="button" class="btn btn-info" @click.prevent="printLabel('dpd_label')">Print DPD Label</button>
                <button type="button" class="btn btn-info" @click.prevent="printLabel('an_post')">Print An Post Label</button>
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
                    previousOrderNumber: null,
                    canClose: true,
                    isPrintingLabel: false,
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
                if (this.order_number) {
                    this.loadOrder(this.order_number);
                }
            },

            watch: {
                order_number() {
                    this.loadOrder(this.order_number);
                },

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
                        this.completeOrder();
                    }
                },
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

                    this.pendingRequests = [];
                    this.canClose = true;
                    if(this.order && this.order['order_number'] !== orderNumber) {
                        this.previousOrderNumber = this.order['order_number'];
                    }
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
                    // this.showLoading();

                    // this.packed = [];

                    const params = {
                        'filter[inventory_source_location_id]': this.getUrlParameter('inventory_source_location_id', null),
                        'filter[order_id]': this.order.id,
                        'sort': 'inventory_source_shelf_location,sku_ordered',
                        'include': 'product,product.aliases',
                        'per_page': 999,
                    };

                    this.apiGetOrderProducts(params)
                        .then(({ data }) => {
                            if(data.meta.total > 0) {
                                const newPackedList = data.data.filter(
                                    orderProduct => Number(orderProduct['quantity_shipped']) >= Number(orderProduct['quantity_ordered'])
                                );

                                const newPacklist = data.data.filter(
                                    orderProduct => Number(orderProduct['quantity_shipped']) < Number(orderProduct['quantity_ordered'])
                                );
                                this.packed = newPackedList;
                                this.packlist = newPacklist;
                            }
                        })
                        .catch( error => {
                            this.notifyError('Error occurred while loading packlist');

                        })
                        .finally(() => {
                            // this.hideLoading();
                        });
                },

                completeOrder: async function () {
                    await this.markAsPacked();
                    await this.printLabelIfNeeded();

                    if (this.user['ask_for_shipping_number'] === true) {
                        this.askForShippingNumberIfNeeded();
                        return;
                    }

                    if(this.order['is_packed'] && this.canClose) {
                        this.$emit('orderCompleted')
                    }
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

                changeStatus(status_code) {
                    this.$refs.filtersModal.hide();

                    return this.apiUpdateOrder(this.order['id'], {
                            'status_code': status_code
                        })
                        .then(() => {
                            this.notifySuccess('Status changed')
                        })
                        .catch(() => {
                            this.notifyError('Error when changing status');
                        });
                },

                askForShippingNumberIfNeeded() {
                    if (this.user['ask_for_shipping_number'] !== true) {
                        return;
                    }
                    this.askForShippingNumber();
                },

                askForShippingNumber() {
                    this.$refs.filtersModal.hide();
                    $('#shippingNumberModal').modal();
                },

                addShippingNumber(shipping_number) {
                    let data = {
                        'order_id': this.order['id'],
                        'shipping_number': shipping_number,
                    };
                    this.apiPostOrderShipment(data)
                        .then(() => {
                            if(this.order['is_packed']) {
                                this.$emit('orderCompleted')
                            }
                            this.notifySuccess('Shipping number saved');
                        })
                        .catch( error => {
                            this.notifyError('Error saving shipping number, try again');
                        })
                },

                markAsPacked: async function () {
                    this.order['is_packed'] = true;

                    this.apiUpdateOrder(this.order['id'],{
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

                setQuantityShipped(orderProduct, quantity) {
                    this.somethingHasBeenPackedDuringThisSession = true;

                    const request = this.apiUpdateOrderProduct(orderProduct.id, {
                            'quantity_shipped': quantity
                        })
                        .then(data => {
                            this.notifySuccess();
                        })
                        .catch(error => {
                            this.notifyError('Error occurred when saving quantity shipped, try again');
                        })
                        .finally(() => {
                            this.loadProducts();
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

                    if(!pickItem) {
                        this.notifyError(`"${barcode}" not found on packlist!`);
                        return;
                    }

                    if (pickItem['quantity_ordered'] - pickItem['quantity_shipped'] < 1) {
                        this.notifyError(`SKU already shipped`);
                        return;
                    }

                    this.setQuantityShipped(pickItem,pickItem['quantity_shipped'] + 1);
                },

                printLabelIfNeeded: async function() {
                    if (!this.user.address_label_template) {
                        return ;
                    }

                    let template = this.user.address_label_template;

                    return await this.printLabel(template);
                },

                printLabel: async function(template) {
                    let orderNumber = this.order['order_number'];

                    this.$refs.filtersModal.hide();

                    return this.apiPrintLabel(orderNumber, template)
                        .catch((error) => {

                            this.canClose = false;
                            let errorMsg = 'Error occurred when printing label';

                            if (error.response.status === 400) {
                                errorMsg = 'Error occurred: ' + error.response.data.message;
                            }

                            if (error.response.status === 403) {
                                errorMsg = 'Error occurred: ' + error.response.data.message;
                            }

                            if (error.response.status === 404) {
                                errorMsg = 'Order not found: #' + orderNumber;
                            }

                            this.notifyError(errorMsg, {
                                closeOnClick: true,
                                timeout: 0,
                                buttons: [
                                    {text: 'OK', action: null},
                                ]
                            });
                        });
                },
                openPreviousOrder: function (){
                    this.$refs.filtersModal.hide();

                    if (! this.previousOrderNumber) {
                        this.notifyError('Not Available');
                        return;
                    }

                    this.loadOrder(this.previousOrderNumber);
                    this.notifySuccess(this.previousOrderNumber);
                }
            },

            computed: {
                showMultipackerWarning()  {
                    if (this.order === null) {
                        return false;
                    }

                    if (this.user === null) {
                        return false;
                    }

                    return (this.order['packer_user_id']  && this.order['packer_user_id'] !== this.user['id']);
                }
            }
        }
    </script>


<style lang="scss">

</style>
