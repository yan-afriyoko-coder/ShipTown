<template>
    <div>
        <div v-if="showScanner" class="overlay" @click.prevent="stopScanner">
            <div id="interactive" class="viewport overlay-content"></div>
        </div>
        <div class="row mb-3 ml-1 mr-1">
            <div class="col-3">Order #: <strong>{{ order === null ? '' : order.order_number}}</strong></div>
            <div class="col-3">Date:<strong>{{ order === null ? '' : order.order_placed_at}}</strong></div>
            <div class="col-3">Lines #: <strong>{{ order === null ? '' : order.product_line_count}}</strong></div>
            <div class="col-3">Quantity #: <strong>{{ order === null ? '' : order.total_quantity_ordered}}</strong></div>
        </div>
        <div class="row mb-3 ml-1 mr-1">
            <div class="col-9">
                <input ref="barcode" class="form-control" placeholder="Scan sku or barcode"
                       v-model="barcode"
                       @focus="simulateSelectAll"
                       @keyup.enter="pickBarcode(barcode)"/>
            </div>
            <div class="col-2">
                <input ref="currentLocation" class="form-control" placeholder="Scan current shelf location"
                       v-model="picklistFilters.currentLocation"
                       @focus="simulateSelectAll"
                       @keyup.enter="updateUrlAndReloadProducts"/>
            </div>
            <div class="col-1">
                <a style="cursor:pointer;" data-toggle="modal" data-target="#picklistConfigurationModal"><font-awesome-icon icon="cog"></font-awesome-icon></a>
            </div>
<!--            <div class="col">-->
<!--                <button type="button" class="btn btn-secondary" @click.prevent="initScanner" href="#"><font-awesome-icon icon="barcode"></font-awesome-icon></button>-->
<!--            </div>-->
        </div>
        <div class="container">
            <div v-if="packlist !== null && packlist.length === 0 && !isLoading" class="row" >
                <div class="col">
                    <div class="alert alert-info" role="alert">
                        No products found.
                    </div>
                </div>
            </div>
            <template v-else class="row">
                <template v-for="record in packlist">
                    <packlist-entry :picklistItem="record"
                                   :key="record.id"
                                   @swipeRight="pickAll"
                                   @swipeLeft="skipPick" />
                </template>
                <template v-for="record in packed">
                    <packlist-entry :picklistItem="record"
                                   :key="record.id"
                                   @swipeRight="pickAll"
                                   @swipeLeft="skipPick" />
                </template>
            </template>
        </div>

        <!--     Modal -->
        <packlist-configuration-modal :picklistFilters="picklistFilters"
                                      id='picklistConfigurationModal'
                                      @btnSaveClicked="onConfigChange" />

    </div>

</template>

<script>
    import beep from '../../mixins/beep';
    import loadingOverlay from '../../mixins/loading-overlay';
    import quagga from '../../mixins/quagga-scanner';

    import PacklistEntry from './PacklistEntry';
    import PicklistConfigurationModal from './ConfigurationModal.vue';

    import VueRouter from 'vue-router';

    Vue.use(VueRouter);

    const Router = new VueRouter({
        mode: 'history',
    });

    export default {
        router: Router,

        mixins: [loadingOverlay, beep, quagga],

        components: {
            'packlist-entry': PacklistEntry,
            'picklist-configuration-modal': PicklistConfigurationModal,
        },

        data: function() {
            const $urlParameters = this.$router.currentRoute.query;
            return {
                picklistFilters: {
                    include: 'packlist',
                    single_line_orders_only: this.getValueOrDefault($urlParameters.single_line_orders, false),
                    currentLocation: this.getValueOrDefault($urlParameters.currentLocation,  ''),
                    inventory_location_id: this.getValueOrDefault($urlParameters.inventory_location_id,  100),
                    in_stock_only: this.getValueOrDefault($urlParameters.in_stock_only,  true),
                },
                order: null,
                packlist: [],
                packed: [],
                barcode: '',
                showScanner: false,
            };
        },

        watch: {
            picklistFilters: {
                handler() {
                    this.updateUrl();
                },
                deep:true
            },
            order() {
                if(this.order !== null) {
                    this.loadPacklist();
                }
            },
            packlist: {
                handler() {
                    if(!this.isLoading && this.packlist.length === 0) {
                        this.loadOrder();
                    }
                }
            }

        },

        mounted() {
            this.updateUrlAndReloadProducts();
            this.setFocusOnBarcodeInput();
            this.simulateSelectAll();
        },

        methods: {
            updateUrl: function() {
                history.pushState({},null,'/packlist?'
                    // +'single_line_orders='+this.picklistFilters.single_line_orders_only
                    // +'&currentLocation='+ this.picklistFilters.currentLocation
                    +'&inventory_location_id='+ this.picklistFilters.inventory_location_id
                    // +'&in_stock_only='+ this.picklistFilters.in_stock_only
                );
            },

            loadOrder: function() {
                console.log('Fetching order');
                this.showLoading();

                this.order = null;
                this.packlist = [];

                axios.get('/api/orders', {
                    params: {
                        'filter[is_picked]': true,
                        'filter[is_packed]': false
                    }})
                    .then(({ data }) => {
                        if(data.total > 0) {
                            this.order = data.data[0];
                        }
                        this.hideLoading();
                    })
                    .catch( error => {
                        this.$snotify.error('Error occurred while loading packlist');
                        this.hideLoading();
                    })


            },

            loadPacklist: function() {
                console.log('Fetching packlist');
                this.packlist = [];
                if(!this.isLoading) {
                    this.showLoading();
                }

                axios.get('/api/packlist', {
                    params: {
                        'include': 'product',
                        'filter[order_id]': this.order.id
                    }})
                    .then(({ data }) => {
                        if(data.total > 0) {
                            data.data.forEach(element => {
                                console.log(element.is_packed);
                                if(element.is_packed === true) {
                                    this.packed.unshift(element);
                                } else {
                                    this.packlist.unshift(element);
                                }
                            });
                        }
                        this.hideLoading();
                    })
                    .catch( error => {
                        this.$snotify.error('Error occurred while loading packlist');
                        this.hideLoading();
                    })


            },

            skipPick(pickedItem) {
                this.packed.splice(this.packlist.indexOf(pickedItem), 1);

                return this.updatePick(pickedItem.id, 0, true)
                    .then( response => {
                        this.picklistFilters.currentLocation = this.getValueOrDefault(pickedItem.shelve_location, '');
                        this.displaySkippedNotification(pickedItem);
                        this.warningBeep();
                    })
                    .catch( error  => {
                        this.packlist.unshift(pickedItem);
                        this.$snotify.error('Not skipped (Error '+ error.response.status+')');
                        this.errorBeep();
                    });
            },

            pickAll(pickedItem) {

                return this.updatePick(pickedItem.id, pickedItem.quantity_requested, true)
                    .then( response => {
                        pickedItem.is_packed = true;
                        this.packlist.splice(this.packlist.indexOf(pickedItem), 1);
                        this.packed.unshift(pickedItem);
                        this.picklistFilters.currentLocation = this.getValueOrDefault(pickedItem.shelve_location, '');
                        this.displayPickedNotification(pickedItem, pickedItem.quantity_requested);
                        this.beep();
                    })
                    .catch( error  => {
                        this.packlist.unshift(pickedItem,1,pickedItem);
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

            resetPick(pick) {
                this.updatePick(pick.id, 0, false)
                    .then(() => {
                        this.$snotify.warning('Action reverted');
                        this.packlist.unshift(pick);
                    });
            },

            displayPickedNotification: function (pickedItem, quantity) {
                const msg =  quantity + ' x ' + pickedItem.sku_ordered + ' picked';
                this.$snotify.confirm('', msg, {
                    timeout: 5000,
                    pauseOnHover: true,
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

            displaySkippedNotification: function (pickedItem) {
                const msg = 'Pick skipped';
                this.$snotify.warning('', msg, {
                    timeout: 5000,
                    pauseOnHover: true,
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

            findPickItem: function (barcode) {
                for (let element of this.packlist) {

                    if(element.sku_ordered === barcode) {
                        return element;
                    }

                    if(element.product === null){
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

            pickBarcode: function (barcode) {
                if(barcode === '') {
                    return;
                }

                let pickItem = this.findPickItem(barcode);

                if(pickItem) {
                    this.pickAll(pickItem);
                    this.setFocusOnBarcodeInput();
                    this.simulateSelectAll();
                    return;
                }

                this.$snotify.error(`"${barcode}" not found on picklist!`);
                this.setFocusOnBarcodeInput();
                this.simulateSelectAll();
            },

            onConfigChange: function(config) {
                this.updateUrlAndReloadProducts();
                this.setFocusOnBarcodeInput();
                this.simulateSelectAll();
            },

            updateUrlAndReloadProducts() {
                this.updateUrl();
                this.loadOrder();
                // return this.fetchPicklist();
            },

            setFocusOnBarcodeInput() {
                this.$refs.barcode.focus();
            },

            simulateSelectAll() {
                setTimeout(() => { document.execCommand('selectall', null, false); });
            },

            getValueOrDefault: function (value, defaultValue){
                return (value === undefined) || (value === null) ? defaultValue : value;
            },

        },


    }
</script>

<style>
.overlay {
  height: 100%;
  width: 100%;
  position: fixed; /* Stay in place */
  z-index: 2; /* Sit on top */
  left: 0;
  top: 0;
  background-color: rgb(0,0,0); /* Black fallback color */
  background-color: rgba(0,0,0, 0.9); /* Black w/opacity */
  overflow-x: hidden; /* Disable horizontal scroll */
  transition: 0.5s; /* 0.5 second transition effect to slide in or slide down the overlay (height or width, depending on reveal) */
}

/* Position the content inside the overlay */
#interactive video {
  position: fixed;
  top: 10%; /* 25% from the top */
  width: 100%; /* 100% width */
  margin-top: 30px; /* 30px top margin to avoid conflict with the close button on smaller screens */
}

/* When the height of the screen is less than 450 pixels, change the font-size of the links and position the close button again, so they don't overlap */
@media screen and (max-height: 450px) {
  .overlay a {font-size: 20px}
  .overlay .closebtn {
    font-size: 40px;
    top: 15px;
    right: 35px;
  }
}
</style>
