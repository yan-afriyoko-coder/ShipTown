<template>
    <div>
        <div class="input-wrapper w-100">
            <input :id="getInputId"
                   :placeholder="placeholder"
                   :disabled="disabled"
                   type=text
                   class="form-control barcode-input"
                   autocomplete="off"
                   autocapitalize="off"
                   enterkeyhint="done"
                   ref="barcode"
                   dusk="barcode-input-field"
                   v-model.trim="barcode"
                   @keyup.enter="barcodeScanned(barcode)"
            />
            <div class="">
                <button v-if="showManualSearchButton" @click="findProduct" type="button" class="btn button-search text-secondary ml-1 md:ml-2">
                    <font-awesome-icon icon="magnifying-glass" class="text-secondary" />
                </button>
                <button @click="scanBarcode(barcodeScanned)" type="button" class="btn button-barcode text-secondary ml-5 md:ml-2">
                    <img src="/barcode-generator?barcode_type=C39&output_type=SVG&content=S&color=gray" alt="">
                </button>
            </div>
        </div>

        <barcode-scanner @modalHidden="setFocusOnBarcodeInput"/>

        <div style="position: fixed; left: 0; bottom: 0; height: 30px;" class="w-100 text-center" v-if="showOnScreenScannerButton">
            <div @click="scanBarcode(barcodeScanned)" class="btn btn-outline-primary rounded-circle bg-warning shadow" style="opacity: 85%; border: solid 2px black; height: 60px; width: 60px; position: relative; top: -40px; font-size: 24pt; color: black;">
                <img src="/barcode-generator?barcode_type=C39&output_type=SVG&content=S&color=dark gray" alt="" style="position: relative; top: -6px;">
            </div>
        </div>

        <b-modal :id="getModalID" scrollable no-fade hide-header
               @submit="updateShelfLocation"
               @shown="updateShelfLocationShown"
               @hidden="updateShelfLocationHidden">
          <div class="h5 text-center">{{ command['name'] }} : {{ command['value'] }}</div>
          <div v-if="shelfLocationModalContinuesScan" class="alert-success text-center mb-2 small">CONTINUES SCAN ENABLED</div>

          <input id="set-shelf-location-command-modal-input"
                 :placeholder="'Scan product to update shelf location: ' + command[1]"
                 type=text
                 class="form-control"
                 autocomplete="off"
                 autocapitalize="off"
                 enterkeyhint="done"
                 @focus="simulateSelectAll"
                 @keyup.enter="updateShelfLocation"/>

          <div class="mt-2 small">
              <div>
                  <span class="text-primary font-weight-bold">Continues Scan</span><span>- scan shelf again to enable</span>
              </div>
              <div>
                  <span class="text-danger font-weight-bold">Close</span><span>- scan twice to close</span>
              </div>
          </div>

          <template #modal-footer>
              <b-button class="mr-auto" variant="primary" :href="`/tools/printer?search=${command['value']}`">Reprint</b-button>
              <b-button variant="secondary" class="float-right" @click="$bvModal.hide(getModalID)">
                  Cancel
              </b-button>
              <b-button variant="primary" class="float-right" @click="$bvModal.hide(getModalID)">
                  OK
              </b-button>
          </template>
      </b-modal>
    </div>
</template>

<script>
import url from "../../mixins/url";
import FiltersModal from "../Packlist/FiltersModal";
import api from "../../mixins/api";
import BarcodeScanner from "../../modals/BarcodeScannerModal.vue";
import helpers from "../../helpers";

export default {
        name: "BarcodeInputField",
        components: {BarcodeScanner},

        mixins: [helpers, url, FiltersModal, api],

        props: {
            input_id: null,
            url_param_name: null,
            placeholder: '',
            showManualSearchButton: {
                type: Boolean,
                default: false,
            },
            showKeyboardOnFocus: {
                type: Boolean,
                default: false,
            },
            disabled: {
                type: Boolean,
                default: false,
            },
            autoFocusAfter: {
                type: Number,
                default: 100,
            },
        },

        computed: {
            getInputId() {
                if (this.input_id) {
                    return this.input_id;
                }

                return `barcode-input-field-${Math.floor(Math.random() * 10000000)}`;
            },

            getModalID() {
                return `set-shelf-location-command-modal-${Math.floor(Math.random() * 10000000)}`;
            },
        },

        data: function() {
            return {
                typedInText: '',
                currentLocation: '',
                barcode: '',
                command: ['',''],

                showOnScreenScannerButton: true,

                shelfLocationModalCommandScanCount: 0,
                shelfLocationModalShowing: false,
                shelfLocationModalContinuesScan: false,
            }
        },

        mounted() {
            this.showOnScreenScannerButton = localStorage.showOnScreenScannerButton === 'true' || localStorage.showOnScreenScannerButton === undefined;

            const isIos = () => !!window.navigator.userAgent.match(/iPad|iPhone/i);

            if (isIos()) {
                console.log('On iPhones and iPads, devices autofocus on input fields is disabled due to a bug in iOS. This works ok with external keyboards on iOS >16');
            }

            this.importValueFromUrlParam();

            if (this.autoFocusAfter > 0) {
                this.setFocusElementById(this.getInputId, this.showKeyboardOnFocus)
            }

            window.addEventListener('keydown', (e) => {
                if (e.target.nodeName !== 'BODY') {
                    return;
                }

                if (e.ctrlKey || e.metaKey || e.altKey || e.shiftKey) {
                    return;
                }

                if (e.key === 'Enter') {
                    this.barcode = this.typedInText;
                    this.barcodeScanned(this.typedInText);
                    return;
                }

                this.typedInText += e.key;
            });
        },

        methods: {
            findProduct() {
                this.$modal.showFindProductModal(this.barcodeFoundManuallyCallback);
            },

            barcodeFoundManuallyCallback(product) {
                this.barcode = product['sku'];
                this.barcodeScanned(product['sku']);
            },

            scanBarcode(atBarcodeScannedCallback) {
                this.$modal.showBarcodeScanner(atBarcodeScannedCallback);
            },

            onScanSuccess (decodedText) {
                document.activeElement.value = decodedText;
                this.html5QrcodeScanner.stop();
                this.$bvModal.hide(this.getScannerModalID);
            },

            barcodeScanned(barcode) {
                if (barcode && barcode !== '') {
                    this.apiPostActivity({
                      'log_name': 'search',
                      'description': barcode,
                    })
                    .catch((error) => {
                        this.displayApiCallError(error)
                    });
                }

                if (this.tryToRunCommand(barcode)) {
                    this.barcode = '';
                    this.typedInText = '';
                    return;
                }

                if(this.url_param_name) {
                    this.setUrlParameter(this.url_param_name, barcode);
                }

                this.$emit('barcodeScanned', barcode);
                this.typedInText = '';
                this.barcode = barcode;

                this.setFocusOnBarcodeInput();
            },

            updateShelfLocationShown: function (bvEvent, modalId) {
                this.shelfLocationModalShowing = true;
                this.shelfLocationModalContinuesScan = false;
                this.shelfLocationModalCommandScanCount = 0;
                this.setFocusElementById('set-shelf-location-command-modal-input')
            },

            updateShelfLocationHidden: function (bvEvent, modalId) {
                this.shelfLocationModalShowing = false;
                this.shelfLocationModalContinuesScan = false;
                this.shelfLocationModalCommandScanCount = 0;
                this.importValueFromUrlParam();
                this.setFocusElementById(this.getInputId)
                this.$emit('refreshRequest');
            },

            importValueFromUrlParam: function () {
                if (this.url_param_name) {
                    this.barcode = this.getUrlParameter(this.url_param_name);
                }
            },

            showShelfLocationModal: function () {
                this.$bvModal.show(this.getModalID);
                this.warningBeep();
                this.setFocusElementById('set-shelf-location-command-modal-input')
            },

            tryToRunCommand: function (textEntered) {
                if (textEntered === null || textEntered === '') {
                    return false;
                }

                let command = textEntered
                    .replace('https://myshiptown.com/?qr=', '')
                    .split(':');

                if(command.length < 2) {
                    return false;
                }

                this.command['name'] = command[0];
                this.command['value'] = command[1];

                switch (this.command['name'].toLowerCase())
                {
                    case 'shelf':
                        this.showShelfLocationModal(this.lastCommand);
                        return true;
                    case 'goto':
                        this.runGotoCommand();
                        return true;
                }

                return false;
            },

            runGotoCommand() {
                window.location.href = this.command['value'];
            },

            updateShelfLocation(event) {
                const textEntered = event.target.value;

                if (textEntered === "") {
                    return;
                }

                let lastCommand = this.command['name'] + ':' + this.command['value'];

                if (textEntered === lastCommand) {
                    event.target.value = '';

                    if (this.shelfLocationModalContinuesScan) {
                        this.$bvModal.hide(this.getModalID);
                        return;
                    }

                    this.shelfLocationModalContinuesScan = true;
                    return;
                }

                this.apiInventoryGet({
                        'filter[sku_or_alias]': textEntered,
                        'filter[warehouse_id]': this.currentUser()['warehouse_id'],
                    })
                    .then((response) => {
                        if (response.data['meta']['total'] !== 1) {
                            this.notifyError('SKU "'+ event.target.value +'" not found ');
                            return;
                        }

                        const inventory = response.data.data[0];
                        this.apiInventoryPost({
                                'id': inventory['id'],
                                'shelve_location': this.command['value'],
                            })
                            .then(() => {
                                if (! this.shelfLocationModalContinuesScan) {
                                    this.$bvModal.hide(this.getModalID);
                                }
                                this.notifySuccess('Shelf updated');
                            })
                            .catch((error) => {
                                this.displayApiCallError(error)
                            });
                    })
                    .catch((error) => {
                        this.displayApiCallError(error)
                    });

                if(this.shelfLocationModalContinuesScan) {
                    this.setFocusElementById('set-shelf-location-command-modal-input')
                }
            },

            setFocusOnBarcodeInput(showKeyboard = false, autoSelectAll = true, delay = 100) {
                this.showOnScreenScannerButton = (localStorage.showOnScreenScannerButton === 'true') || (localStorage.showOnScreenScannerButton === undefined);
                this.setFocusElementById(this.getInputId, showKeyboard, autoSelectAll, delay)
            },
        }
    }
</script>

<style scoped>
.barcode-input::selection {
    color: black;
    background: #cce3ff;
}

.input-wrapper {
    width: 100%;
    position: relative;
    display: inline-block;
}

.input-wrapper input {
    padding-right: 80px;
}

.input-wrapper button {
    position: absolute;
    border: none;
    background-color: transparent;
    cursor: pointer;
}

.button-barcode {
    right: 0;
    top: 0;
}

.button-search {
    right: 42px;
    top: 1px;
}
</style>
