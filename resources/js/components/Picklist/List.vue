<template>
    <div>
        <div v-if="showScanner" class="overlay" @click.prevent="stopScanner">
            <div id="interactive" class="viewport overlay-content"></div>
        </div>
        <div class="row mb-3 ml-1 mr-1">
            <div class="col-9">
                <input ref="barcode"
                       class="form-control"
                       v-model="barcode"
                       @focus="simulateSelectAll"
                       @keyup.enter="pickBarcode"
                       placeholder="Scan sku or barcode" />
            </div>
            <div class="col-2">
                <input ref="currentLocation"
                       v-model="picklistFilters.currentLocation"
                       class="form-control"
                       @focus="simulateSelectAll"
                       @keyup.enter="updateUrlAndReloadProducts"
                       placeholder="Scan current shelf location" />
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
                                   @transitionEnd="pickProduct" />
                </template>
            </template>
        </div>

        <!--     Modal -->
        <picklist-configuration-modal :picklistFilters="picklistFilters" id='picklistConfigurationModal' @btnSaveClicked="onConfigChange" />

    </div>

</template>

<script>
    import Quagga from 'quagga';

    import loadingOverlay from '../../mixins/loading-overlay';
    import PicklistItem from './PicklistItem';
    import PicklistConfigurationModal from './ConfigurationModal.vue';

    import VueRouter from 'vue-router';

    Vue.use(VueRouter);

    const Router = new VueRouter({
        mode: 'history',
        routes: [
            {
                path: '/picklist',
                // name: 'picklist',
                // component: Home
            },
        ],
    });

    export default {
        router: Router,

        mixins: [loadingOverlay],

        components: {
            'picklist-item': PicklistItem,
            'picklist-configuration-modal': PicklistConfigurationModal,
        },

        data: function() {
            const $urlParameters = this.$router.currentRoute.query;
            return {
                picklistFilters: {
                    single_line_orders_only: this.setDefaultVal($urlParameters.single_line_orders, false),
                    currentLocation: this.setDefaultVal($urlParameters.currentLocation,  ''),
                },
                barcode: '',
                picklist: [],
                showScanner: false,
            };
        },

        mounted() {
            this.updateUrlAndReloadProducts();
            this.setFocusOnBarcodeInput();
        },

        watch: {
            picklistFilters: {
                handler() {
                    this.updateUrl();
                },
                deep:true
            }
        },

        methods: {
            fetchPicklist: function() {
                return new Promise((resolve, reject) => {

                    this.showLoading();

                    axios.get('/api/picklist', {params: this.picklistFilters})

                        .then(({ data }) => {
                            this.picklist = data.data;
                            resolve(data);
                        })

                        .catch(reject)

                        .then(() => {
                            this.hideLoading();
                        });
                });
            },

            pickProduct(pickedItem) {

                axios.post(`/api/picklist/${pickedItem.id}`,
                    {'quantity_picked': pickedItem.quantity_requested})

                    .then(({ data }) => {
                        this.picklistFilters.currentLocation = pickedItem.shelve_location;
                        this.picklist.splice(this.picklist.indexOf(pickedItem), 1);
                        this.displayNotification(pickedItem);
                        if(this.picklist.length === 0) {
                            this.updateUrlAndReloadProducts();
                        }
                    })
                    .catch( data  => {
                        this.$snotify.error(`Items not picked.`);
                    });
            },

            displayNotification: function (pickedItem) {
                let itemIndex = this.picklist.indexOf(pickedItem);
                const msg =  pickedItem.quantity_requested + ' x ' + pickedItem.sku_ordered + ' picked';
                this.$snotify.confirm('', msg, {
                    timeout: 5000,
                    pauseOnHover: true,
                    buttons: [
                        {
                            text: 'Undo',
                            action: (toast) => {
                                this.$snotify.remove(toast.id);
                                axios.post(`/api/picklist/${pickedItem.id}`, {'quantity_picked': pickedItem.quantity_requested * -1})
                                    .then(() => {
                                        this.$snotify.warning('Action reverted');
                                        this.picklist.splice(itemIndex, 0, pickedItem);
                                    });
                            }
                        }
                    ]
                });
            },

            pickBarcode: function () {

                let pickedItem;
                let barcode = this.barcode;

                if(barcode === '') {
                    return;
                }

                for (let element of this.picklist) {
                    if(element.sku_ordered === barcode) {
                        pickedItem = element;
                        break;
                    }
                }

                if (typeof pickedItem == 'undefined') {
                    this.$snotify.error(`"${barcode}" not found!`);
                    this.simulateSelectAll();
                    return;
                }

                this.pickProduct(pickedItem);

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
                );
            },

            setFocusOnBarcodeInput() {
                this.$refs.barcode.focus();
            },

            simulateSelectAll() {
                setTimeout(() => { document.execCommand('selectall', null, false); });
            },

            initScanner(e) {
                this.showScanner = true;
                this.$nextTick(() => {
                    Quagga.init({
                        decoder : {
                            readers : [
                                "code_39_reader",
                                "ean_reader",
                                "code_128_reader",
                            ],
                            debug: {
                                drawBoundingBox: false,
                                showFrequency: false,
                                drawScanline: false,
                                showPattern: false
                            }
                        },
                        inputStream: {
                            name: "Live",
                            type: "LiveStream",
                            area: { // defines rectangle of the detection/localization area
                                top: "0%",    // top offset
                                right: "0%",  // right offset
                                left: "0%",   // left offset
                                bottom: "0%"  // bottom offset
                            },
                            singleChannel: false // true: only the red color-channel is read
                        }
                    }, function(err) {
                        if (err) {
                            console.log(err);
                            //return
                        }
                        console.log("Initialization finished. Ready to start");
                        Quagga.start();
                    });


                    Quagga.onDetected((data) => {
                        this.query = data.codeResult.code;
                        this.updateUrlAndReloadProducts();
                        this.stopScanner();
                    })
                });
            },

            stopScanner() {
                this.showScanner = false;
                Quagga.stop();
            },

            setDefaultVal: function (value, defaultValue){
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
