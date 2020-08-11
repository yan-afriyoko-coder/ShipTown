<template>
    <div>
        <div v-if="showScanner" class="overlay" @click.prevent="stopScanner">
            <div id="interactive" class="viewport overlay-content"></div>
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
            <div v-if="picklist.length === 0 && !isLoading" class="row" >
                <div class="col">
                    <div class="alert alert-info" role="alert">
                        No products found.
                    </div>
                </div>
            </div>
            <template v-else class="row">
                <template v-for="picklistItem in picklist">
                    <picklist-item :picklistItem="picklistItem"
                                   :key="picklistItem.id"
                                   @swipeRight="pickAll"
                                   @swipeLeft="skipPick" />
                </template>
            </template>
        </div>

        <!--     Modal -->
        <picklist-configuration-modal :picklistFilters="picklistFilters"
                                      id='picklistConfigurationModal'
                                      @btnSaveClicked="onConfigChange" />

    </div>

</template>

<script>
    import beep from '../../mixins/beep';
    import loadingOverlay from '../../mixins/loading-overlay';
    import quagga from '../../mixins/quagga-scanner';

    import PicklistItem from './PicklistItem';
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
            'picklist-item': PicklistItem,
            'picklist-configuration-modal': PicklistConfigurationModal,
        },

        data: function() {
            const $urlParameters = this.$router.currentRoute.query;
            return {
                picklistFilters: {
                    include: 'product.aliases,product,order',
                    single_line_orders_only: this.getValueOrDefault($urlParameters.single_line_orders, false),
                    currentLocation: this.getValueOrDefault($urlParameters.currentLocation,  ''),
                    inventory_location_id: this.getValueOrDefault($urlParameters.inventory_location_id,  100),
                },
                barcode: '',
                picklist: [],
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
            picklist: {
                handler() {
                    if (this.picklist.length === 0) {
                        this.updateUrlAndReloadProducts();
                    }
                }
            },
        },

        mounted() {
            this.updateUrlAndReloadProducts();
            this.setFocusOnBarcodeInput();
            this.simulateSelectAll();
        },

        methods: {
            fetchPicklist: function() {
                return new Promise((resolve, reject) => {

                    this.showLoading();

                    axios.get('/api/picklist', {params: this.picklistFilters})

                        .then(({ data }) => {
                            if(data.data.length > 0) {
                                this.picklist = data.data;
                            }
                            resolve(data);
                        })

                        .catch(reject)

                        .then(() => {
                            this.hideLoading();
                        });
                });
            },

            skipPick(pickedItem) {
                this.picklist.splice(this.picklist.indexOf(pickedItem), 1);

                return this.updatePick(pickedItem.id, 0, true)
                    .then( response => {
                        this.picklistFilters.currentLocation = this.getValueOrDefault(pickedItem.shelve_location, '');
                        this.displaySkippedNotification(pickedItem);
                        this.warningBeep();
                    })
                    .catch( error  => {
                        this.picklist.unshift(pickedItem);
                        this.$snotify.error('Not skipped (Error '+ error.response.status+')');
                        this.errorBeep();
                    });
            },

            pickAll(pickedItem) {
                this.picklist.splice(this.picklist.indexOf(pickedItem), 1);

                return this.updatePick(pickedItem.id, pickedItem.quantity_requested, true)
                    .then( response => {
                        this.picklistFilters.currentLocation = this.getValueOrDefault(pickedItem.shelve_location, '');
                        this.displayPickedNotification(pickedItem, pickedItem.quantity_requested);
                        this.beep();
                    })
                    .catch( error  => {
                        this.picklist.unshift(pickedItem);
                        this.$snotify.error('Items not picked (Error '+ error.response.status+')');
                        this.errorBeep();
                    });
            },

            updatePick(pickId, quantityPicked, isPicked) {
                return axios.post(`/api/picklist/${pickId}`, {
                    'quantity_picked': quantityPicked,
                    'is_picked': isPicked
                });
            },

            resetPick(pick) {
                this.updatePick(pick.id, 0, false)
                    .then(() => {
                        this.$snotify.warning('Action reverted');
                        this.picklist.unshift(pick);
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
                for (let element of this.picklist) {

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
                return this.fetchPicklist();
            },

            updateUrl: function() {
                history.pushState({},null,'/picklist?'
                    +'single_line_orders='+this.picklistFilters.single_line_orders_only
                    +'&currentLocation='+ this.picklistFilters.currentLocation
                    +'&inventory_location_id='+ this.picklistFilters.inventory_location_id
                );
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
