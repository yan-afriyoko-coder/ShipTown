 <template>
    <div>
        <div v-if="getUrlParameter('step', '') === 'select'">
            <div v-for="bookmark in bookmarks" class="col-12 mt-1">
                <a dusk="startAutopilotButton" type="button" class="btn btn-primary col" :href="bookmark['url']">
                    {{ bookmark['name'] }}
                </a>
            </div>
        </div>

        <div v-else>
            <search-and-option-bar-observer/>
            <search-and-option-bar :isStickable="true">
                <div class="d-flex flex-nowrap">
                    <div class="flex-fill">
                        <barcode-input-field
                            placeholder="Enter sku or alias"
                            ref="barcode"
                            :url_param_name="'search'"
                            @barcodeScanned="pickByBarcode"
                        />
                    </div>
                    <div style="width: 60px" class="ml-2">
                        <input
                            ref="current_location"
                            class="form-control w-100"
                            placeholder="Shelf"
                            v-model="current_shelf_location"
                            @keyup.enter="reloadPicks()"
                        />
                    </div>
                </div>
                <template v-slot:buttons>
                    <top-nav-button v-b-modal="'configuration-modal'"/>
                </template>
            </search-and-option-bar>


            <div class="row pl-2 p-1 font-weight-bold text-uppercase small text-secondary">
                <div class="col-6 text-left text-nowrap">
                    TOOLS > PICKLIST
                </div>
                <div class="col-6 text-right text-nowrap">
                    <!--                        -->
                </div>
            </div>
            <div v-if="picklist !== null && picklist.length === 0" class="row" >
                <div class="col">
                    <div class="alert alert-info" role="alert">
                        No picks found
                    </div>
                </div>
            </div>

            <div v-else>
                <template v-for="pick in picklist">
                    <pick-card :pick="pick" :id="`pick-card-${ picklist.indexOf(pick)}`" @swipeRight="pickAll" @swipeLeft="partialPickSwiped"/>
                </template>
            </div>

            <div class="row" v-if="isLoading">
                <div class="col">
                    <div ref="loadingContainerOverride" style="height: 32px"></div>
                </div>
            </div>

        </div>
        <b-modal id="quick-actions-modal" no-fade hide-header @shown="setFocusElementById('stocktake-input',)" @hidden="setFocusOnBarcodeInput">
            <stocktake-input></stocktake-input>
            <template #modal-footer>
                <b-button variant="secondary" class="float-right" @click="$bvModal.hide('quick-actions-modal');">
                    Cancel
                </b-button>
                <b-button variant="primary" class="float-right" @click="$bvModal.hide('quick-actions-modal');">
                    OK
                </b-button>
            </template>
        </b-modal>

    </div>
</template>

<script>
import PickCard from "./Picks/PickCard.vue";
import loadingOverlay from '../mixins/loading-overlay';
import BarcodeInputField from "./SharedComponents/BarcodeInputField";
import PicklistConfigurationModalNew from './Picks/ConfigurationModal.vue';

import url from "../mixins/url";
import beep from "../mixins/beep";
import api from "../mixins/api";
import Vue from "vue";
import helpers from "../mixins/helpers";

export default {
    name: "PicksTable",

    mixins: [loadingOverlay, url, beep, api, BarcodeInputField, helpers],

    components: {
        'pick-card': PickCard,
        'barcode-input-field': BarcodeInputField,
        'picklist-configuration-modal': PicklistConfigurationModalNew   ,
    },

    mounted() {
        if (Vue.prototype.$currentUser['warehouse_id'] === null) {
            this.$snotify.error('You do not have warehouse assigned. Please contact administrator', {timeout: 50000});
            return;
        }

        this.setUrlParameter('warehouse_id', Vue.prototype.$currentUser['warehouse_id']);


        this.apiGetNavigationMenu({
                'filter[group]': 'picklist',
                'per_page': 100
            })
            .then((response) => {
                this.bookmarks = response.data.data;
            })
            .catch((error) => {
                this.displayApiCallError(error);
            });

        if (this.getUrlParameter('step', '') === '') {
            this.reloadPicks();
        }
    },

    watch: {
        picklist: {
            handler() {
                if (this.picklist.length === 0 && this.isLoading === false ) {
                    this.reloadPicks();
                }

            },
            deep: true
        },
        current_shelf_location() {
            this.setUrlParameter('current_shelf_location', this.current_shelf_location)
        }
    },

    data: function() {
        return {
            picklist: null,
            current_shelf_location: '',
            bookmarks: [],
        };
    },

    methods: {
        pickByBarcode(barcode) {
            if (barcode === '') {
                return;
            }

            let pickItem = this.findPickItem(barcode);

            if(!pickItem) {
                this.$snotify.error(`"${barcode}" not found on picklist!`);
                this.errorBeep();
                return;
            }

            this.pickAll(pickItem);
        },

        findPickItem: function (barcode) {
            if(barcode === '') {
                return null;
            }

            for (let element of this.picklist) {

                if(element['sku_ordered'] === barcode) {
                    return element;
                }

                if(element['product'] === null){
                    continue;
                }

                if(element['product']['sku'] === barcode) {
                    return element;
                }

                if(typeof element['product']['aliases'] === 'undefined') {
                    continue;
                }

                for(let alias of element['product']['aliases']) {
                    if(alias['alias'] === barcode){
                        return element;
                    }
                }


            }
            return null;
        },

        reloadPicks() {
            this.showLoading();

            this.setFocusOnBarcodeInput(300);

            const params = {
                'include': 'product,product.aliases',
                'sort': 'inventory_source_shelf_location,sku_ordered',
                'per_page': this.getUrlParameter('per_page', 5),
                'filter[in_stock_only]': this.getUrlParameter('in_stock_only', true),
                'filter[warehouse_id]': this.getUrlParameter('warehouse_id'),
                'filter[current_shelf_location]': this.getUrlParameter('current_shelf_location'),
                'filter[order.status_code]': this.getUrlParameter('order.status_code'),
                'filter[created_between]': this.getUrlParameter('created_between'),
            };

            return this.apiGetPicklist(params)
                .then( ({data}) => {
                    this.picklist = data.data;
                })
                .catch( error => {
                    this.displayApiCallError(error);
                })
                .finally( () => {
                    this.hideLoading();
                });
        },

        postPick(pick, quantity_picked, quantity_skipped_picking) {
            let data = {
                'quantity_picked': quantity_picked,
                'quantity_skipped_picking': quantity_skipped_picking,
                'order_product_ids': pick['order_product_ids'],
            };
            return this.apiPostPicklistPick(data)
                .catch( error => {
                    this.displayApiCallError(error);
                });
        },

        postPickUpdate(pick, quantity_picked) {
            return this.apiPostPicklistPick({
                    'quantity_picked': quantity_picked,
                    'order_product_ids': pick['order_product_ids'],
                })
                .catch( error => {
                    this.displayApiCallError(error);
                });
        },

        deletePick(pick) {
            return this.apiDeletePick(pick['id'])
                .catch( error => {
                    this.displayApiCallError(error);
                });
        },

        removeFromPicklist: function (pick) {
            this.picklist.splice(this.picklist.indexOf(pick), 1);
        },

        pickAll(pick) {
            this.current_shelf_location = pick['inventory_source_shelf_location'];
            this.removeFromPicklist(pick);
            this.setFocusOnBarcodeInput();

            this.postPick(pick, pick['quantity_required'], 0)
                .then( (response) => {
                    this.displayPickedNotification(response.data['data'][0], pick['quantity_required']);
                    this.beep();
                    this.reloadPicks();
                })
                .catch( error => {
                    this.displayApiCallError(error);
                });
        },

        skipPick: function (pick) {
            this.postPick(pick, 0, pick['quantity_required'])
                .then(() => {
                    this.$snotify.warning('Pick deleted', {
                        timeout: 1500,
                        icon: false,
                    });
                    this.warningBeep();
                })
                .then(() => {
                    this.reloadPicks();
                })
        },

        makePartialPick: function (pick, quantity) {
            this.postPickUpdate(pick, quantity)
                .then(() => {
                    this.displayPickedNotification(pick, quantity);
                    this.beep();
                    this.removeFromPicklist(pick);
                    this.reloadPicks();
                });
        },

        partialPickSwiped(pick) {
            this.$snotify.prompt('Partial pick', {
                placeholder: 'Enter quantity picked:',
                position: 'centerCenter',
                icon: false,
                buttons: [
                    {
                        text: 'Delete Pick',
                        action: (toast) => {
                            this.$snotify.remove(toast.id)
                            this.skipPick(pick);
                            this.setFocusOnBarcodeInput();
                        }
                    },
                    {
                        text: 'Pick',
                        action: (toast) => {
                            if ( isNaN(toast.value) || (toast.value <= 0) || (toast.value > Number(pick['quantity_required'])) ) {
                                toast.valid = false;
                                return false;
                            }

                            this.$snotify.remove(toast.id);
                            this.makePartialPick(pick, toast.value);
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

        partialPick(pickedItem) {
            this.$snotify.prompt('Partial pick', {
                placeholder: 'Enter quantity picked:',
                position: 'centerCenter',
                icon: false,
                buttons: [
                    {
                        text: 'Pick',
                        action: (toast) => {
                            if ( isNaN(toast.value) || (toast.value <= 0)) {
                                toast.valid = false;
                                return false;
                            }
                            this.picklist.splice(this.picklist.indexOf(pickedItem), 1);
                            this.$snotify.remove(toast.id);
                            this.pick(pickedItem, toast.value)
                                .then(() => {

                                    this.picklist = [];
                                });

                        }
                    },
                    {
                        text: 'Cancel',
                        action: (toast) => {
                            // this.picklist.unshift(pickedItem);
                            this.$snotify.remove(toast.id) // default
                        }
                    },
                ],
            });
        },

        undoPick(pick) {
            this.showLoading();
            this.apiDeletePick(pick['id'])
                .then( () => {
                    this.$snotify.warning('Action reverted', {
                        timeout: 1500,
                        icon: false,
                    });
                    this.warningBeep();
                })
                .catch( error => {
                    this.displayApiCallError(error);
                })
                .then( () => {
                    this.reloadPicks();
                    this.hideLoading();
                });
        },

        displayPickedNotification: function (pick, quantity) {
            const msg =  Math.ceil(quantity) + ' x ' + pick['sku_ordered'] + ' picked';
            this.$snotify.confirm(msg, {
                timeout: 5000,
                showProgressBar: false,
                pauseOnHover: true,
                icon: false,
                buttons: [
                    {
                        text: 'Undo',
                        action: (toast) => {
                            this.$snotify.remove(toast.id);
                            this.undoPick(pick, quantity)
                        }
                    }
                ]
            });
        },
    },
}
</script>
