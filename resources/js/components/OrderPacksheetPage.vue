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
                        <b>{{ order_comment['user'] ? order_comment['user']['name'] : 'AutoPilot' }}: </b>{{ order_comment['comment'] }}
                    </div>
                </div>
            </template>
            <div class="row mb-3">
                <div class="col-11">
                    <barcode-input-field @barcodeScanned="packBarcode" placeholder="Enter sku or alias to ship 1 piece" ref="barcode"/>
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
                <div class="form-group">
                    <label class="form-label" for="selectStatus">Status</label>
                    <select id="selectStatus" class="form-control" @change="changeStatus" v-model="order.status_code">
                        <option v-for="orderStatus in orderStatuses" :value="orderStatus.code" :key="orderStatus.id">{{ orderStatus.code }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="selectStatus">Courier</label>
                    <select id="courierSelect" class="form-control" @change="updateLabelTemplate" v-model="order.label_template">
                        <option v-for="shippingCourier in shippingCouriers" :value="shippingCourier.code" :key="shippingCourier.code">{{shippingCourier.code}}</option>
                    </select>
                </div>
                <button type="button" class="btn mb-1 btn-info" @click.prevent="printExtraLabelClick()">Print Extra Label</button>
                <br>

                <button type="button" class="btn mb-1 btn-info" @click.prevent="openPreviousOrder">Open Previous Order</button>
                <button type="button" class="btn mb-1 btn-info" @click.prevent="askForShippingNumber">Add Shipping Number</button>
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
    import Vue from "vue";

    export default {
            mixins: [loadingOverlay, beep, url, api, helpers, BarcodeInputField],

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
                    order: null,
                    orderProducts: [],
                    orderStatuses: [],
                    shippingCouriers: [],

                    packlist: null,
                    packed: [],

                    previousOrderNumber: null,
                    canClose: true,
                    isPrintingLabel: false,
                    somethingHasBeenPackedDuringThisSession: false,
                    autopilotEnabled: false,
                };
            },

            watch: {
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

                order() {
                    if(!this.order) {
                        return;
                    }

                    this.apiActivitiesPost({
                        'subject_type': 'order',
                        'subject_id': this.order.id,
                        'message': 'Packsheet opened'
                    })
                    this.loadOrderProducts();
                },

                orderProducts() {
                    if(this.orderProducts.length === 0) {
                        this.packed = [];
                        this.packlist = [];
                    }

                    this.packed = this.orderProducts.filter(orderProduct => Number(orderProduct['quantity_to_ship']) === 0);
                    this.packlist = this.orderProducts.filter(orderProduct => Number(orderProduct['quantity_to_ship']) > 0);
                },
            },


            mounted() {
                this.loadOrder(this.order_number);
                this.loadOrderStatuses();
                this.loadShippingCouriers();

                if (Vue.prototype.$currentUser['warehouse_id']) {
                    this.setUrlParameter('warehouse_id', Vue.prototype.$currentUser['warehouse_id']);

                    if (this.order_number) {
                        this.loadOrder(this.order_number);
                    }
                    return;
                }

                this.$snotify.error('You do not have warehouse assigned. Please contact administrator', {timeout: 50000});
            },

            methods: {
                completeOrder: async function () {
                    await this.markAsPacked();
                    await this.autoPrintLabelIfNeeded();

                    if (Vue.prototype.$currentUser['ask_for_shipping_number'] === true) {
                        this.askForShippingNumber();
                        return;
                    }

                    if((this.packlist.length === 0) && this.canClose) {
                        this.$emit('orderCompleted')
                    }
                },

                loadOrder: function (orderNumber) {
                    this.showLoading();

                    if(this.order && this.order['order_number'] !== orderNumber) {
                        this.previousOrderNumber = this.order['order_number'];
                    }

                    this.canClose = true;
                    this.order = null;
                    this.packlist = [];
                    this.packed = [];
                    this.somethingHasBeenPackedDuringThisSession = false;

                    const params = {
                        'filter[order_number]': orderNumber,
                        'include': 'order_totals,order_comments,' +
                            'order_comments.user',
                    };

                    return this.apiGetOrders(params)
                        .then(({data}) => {
                            this.order = data.meta.total > 0 ? data.data[0] : null;
                        })
                        .catch(() => {
                            this.notifyError('Error occurred while loading order');
                        })
                        .finally(() => {
                            this.hideLoading();
                        })
                },

                loadOrderProducts: function() {
                    const params = {
                        'filter[order_id]': this.order.id,
                        'filter[warehouse_id]': this.getUrlParameter('warehouse_id'),
                        'sort': 'inventory_source_shelf_location,' +
                            'sku_ordered',
                        'include': 'product,' +
                            'product.aliases',
                        'per_page': 999,
                    };

                    this.apiGetOrderProducts(params)
                        .then(({ data }) => {
                            this.orderProducts = data.data;
                        })
                        .catch(() => {
                            this.notifyError('Error occurred while loading packlist');
                        });
                },

                loadOrderStatuses(){
                    this.apiGetOrderStatus({
                            'filter[hidden]': 0,
                            'per_page': 999,
                            'sort': 'code'
                        })
                        .then(({ data }) => {
                            this.orderStatuses = data.data;
                        })
                },

                loadShippingCouriers() {
                    this.apiGetShippingServices({
                        'per_page': 999,
                        'sort': 'code'
                    })
                        .then(({ data }) => {
                            this.shippingCouriers = data.data;
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

                                    const maxQuantityAllowed = orderProduct['quantity_to_ship'];
                                    const minQuantityAllowed = orderProduct['quantity_shipped'] * (-1);

                                    if (
                                        isNaN(toast.value)
                                        || (toast.value > Number(maxQuantityAllowed))
                                        || (toast.value < Number(minQuantityAllowed))
                                    ) {
                                        toast.valid = false;
                                        return false;
                                    }

                                    this.$snotify.remove(toast.id);
                                    this.shipOrderProduct(orderProduct, Number(toast.value));
                                    this.setQuantityShipped(orderProduct, Number(orderProduct['quantity_shipped']) + Number(toast.value));
                                    this.setFocusOnBarcodeInput();
                                }
                            },
                            {
                                text: 'Cancel',
                                action: (toast) => {
                                    this.$snotify.remove(toast.id);
                                    this.setFocusOnBarcodeInput();
                                }
                            },
                        ],
                    });
                },

                changeStatus() {
                    this.$refs.filtersModal.hide();
                    this.setFocusOnBarcodeInput(500);

                    this.apiUpdateOrder(this.order['id'], {'status_code': this.order.status_code})
                        .then((response) => {
                            this.order = response.data.data
                            this.notifySuccess('Status changed')
                        })
                        .catch(() => {
                            this.notifyError('Error when changing status');
                        });
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
                            if(this.packlist.length === 0) {
                                this.$emit('orderCompleted')
                            }
                            this.notifySuccess('Shipping number saved');
                        })
                        .catch(() => {
                            this.notifyError('Error saving shipping number, try again');
                        })
                },

                markAsPacked: async function () {
                    this.order['is_packed'] = true;
                    this.order['packer_user_id'] = Vue.prototype.$currentUser['id'];

                    this.apiUpdateOrder(this.order['id'],{
                            'is_packed': true,
                            'packer_user_id': Vue.prototype.$currentUser['id']
                        })
                        .catch((error) => {
                            this.notifyError('Error: '+error.response.message);
                        });
                },

                shipOrderProduct(orderProduct, quantity) {
                    this.apiPostOrderProductShipment({
                            'sku_shipped': orderProduct.sku_ordered,
                            'product_id': orderProduct.product_id,
                            'order_id': orderProduct.order_id,
                            'order_product_id': orderProduct.id,
                            'quantity_shipped': quantity,
                        })
                        .then((data) => {
                            this.displayPackedNotification(data.data);
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                },

                setQuantityShipped(orderProduct, quantity) {
                    this.somethingHasBeenPackedDuringThisSession = true;

                    this.apiUpdateOrderProduct(orderProduct.id, {'quantity_shipped': quantity})
                        .then(() => {
                            this.notifySuccess();
                        })
                        .catch(() => {
                            this.notifyError('Error occurred when saving quantity shipped, try again');
                        })
                        .finally(() => {
                            this.loadOrderProducts();
                        });
                },

                shipAll(orderProduct) {
                    this.shipOrderProduct(orderProduct, orderProduct['quantity_to_ship']);
                    this.setQuantityShipped(orderProduct, orderProduct['quantity_ordered']);
                    this.setFocusOnBarcodeInput();
                },

                findEntry: function (barcode) {
                    if(barcode === '') {
                        return null;
                    }

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
                    let pickItem = this.findEntry(barcode);

                    if(!pickItem) {
                        this.notifyError(`"${barcode}" not found on packlist!`);
                        return;
                    }

                    if (pickItem['quantity_to_ship'] === 0) {
                        this.notifyError(`SKU already shipped`);
                        return;
                    }

                    this.shipOrderProduct(pickItem, 1);
                    this.setQuantityShipped(pickItem,pickItem['quantity_shipped'] + 1);
                },

                getAddressLabelTemplateName: function () {
                    if (this.getUrlParameter('address_label_template')) {
                        return this.getUrlParameter('address_label_template');
                    }

                    if (this.order.label_template) {
                        return this.order.label_template;
                    }

                    if (Vue.prototype.$currentUser.address_label_template) {
                        return Vue.prototype.$currentUser.address_label_template;
                    }

                    return '';
                },

                autoPrintLabelIfNeeded: async function() {
                    let template = this.getAddressLabelTemplateName();

                    if (template) {
                        return await this.printShippingLabel(template);
                    }
                },

                createShipment: async function(carrier) {
                    let shipment = {
                        'order_id': this.order.id,
                        'shipping_number': 'pending',
                        'carrier': carrier,
                        'service': 'overnight',
                    };

                    this.$refs.filtersModal.hide();

                    return this.apiPostShipment(shipment)
                        .catch((error) => {
                            this.canClose = false;
                            let errorMsg = 'Error: ' + error.response.data.message;

                            this.notifyError(errorMsg, {
                                closeOnClick: true,
                                timeout: 0,
                                buttons: [
                                    {text: 'OK', action: null},
                                ]
                            });
                        });
                },

                printExtraLabelClick: function ()
                {
                    this.$refs.filtersModal.hide();
                    this.setFocusOnBarcodeInput(500);

                    this.printShippingLabel();
                },

                printShippingLabel: async function(shipping_service_code = null) {
                    if (shipping_service_code === null) {
                        shipping_service_code = this.getAddressLabelTemplateName();
                    }

                    let params = {
                        'shipping_service_code': shipping_service_code,
                        'order_id': this.order.id
                    };

                    return this.apiPostShippingLabel(params)
                        .catch((error) => {
                            this.canClose = false;
                            let errorMsg = 'Error ' + error.response.status + ': ' + JSON.stringify(error.response.data);

                            this.notifyError(errorMsg, {
                                closeOnClick: true,
                                timeout: 0,
                                buttons: [
                                    {text: 'OK', action: null},
                                ]
                            });
                        });
                },

                printLabel: async function(template = null) {
                    if (template === null) {
                        template = this.order.label_template;
                    }

                    let orderNumber = this.order['order_number'];

                    this.setFocusOnBarcodeInput();

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
                },

                displayPackedNotification: function (order_product_shipment) {
                    const msg =  order_product_shipment.data.quantity_shipped + ' x ' + '' + ' shipped';
                    this.$snotify.confirm(msg, {
                        timeout: 3000,
                        showProgressBar: false,
                        pauseOnHover: true,
                        icon: false,
                        buttons: []
                    });
                },

                updateLabelTemplate: function () {
                    this.$refs.filtersModal.hide();
                    this.setFocusOnBarcodeInput(500);

                    this.apiUpdateOrder(this.order['id'], {'label_template': this.order.label_template})
                        .then((response) => {
                            this.order = response.data.data
                        })
                        .catch(() => {
                            this.notifyError('Error when changing status');
                        });
                }
            },

            computed: {
                showMultipackerWarning()  {
                    if (this.order === null) {
                        return false;
                    }

                    return (this.order['packer_user_id']  && this.order['packer_user_id'] !== Vue.prototype.$currentUser['id']);
                }
            },
    }
    </script>


<style lang="scss">

</style>
