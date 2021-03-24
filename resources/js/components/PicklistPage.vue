<template>
    <div>

        <div class="row mb-3">
            <div class="col-8">
                <div class="pl-1 pr-1">
                    <barcode-input-field @barcodeScanned="pickByBarcode"/>
                </div>
            </div>
            <div class="col-4">
                <div class="row">
                    <div class="col-7">
                        <input ref="current_location" class="form-control" placeholder="Current shelf"
                               v-model="current_shelf_location"
                               @keyup.enter="reloadPicks()"/>
                    </div>
                    <div class="col-5 ml-0 pl-0">
                        <button type="button" class="btn btn-light" data-toggle="modal" data-target="#picklistConfigurationModal" href="#"><font-awesome-icon icon="cog" class="fa-lg"></font-awesome-icon></button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="picklist.length === 0 && !isLoading" class="row" >
            <div class="col">
                <div class="alert alert-info" role="alert">
                    No picks found
                </div>
            </div>
        </div>

        <div v-else>
            <template v-for="pick in picklist">
                <pick-card
                    :pick="pick"
                    :id="`pick-card-${ picklist.indexOf(pick)}`"
                    @swipeRight="pickAll"
                    @swipeLeft="partialPickSwiped"/>
            </template>
        </div>


        <div class="row">
            <div class="col">
                <div ref="loadingContainerOverride" style="height: 100px"></div>
            </div>
        </div>

        <!--     Modal -->
        <picklist-configuration-modal id='picklistConfigurationModal' @btnSaveClicked="reloadPicks" />

    </div>
</template>

<script>
import PickCard from "./Picks/PickCard.vue";
import loadingOverlay from '../mixins/loading-overlay';
import BarcodeInputField from "./SharedComponents/BarcodeInputField";
import PicklistConfigurationModalNew from './Picks/ConfigurationModal.vue';

import url from "../mixins/url";
import beep from "../mixins/beep";

export default {
    name: "PicksTable",

    mixins: [loadingOverlay, url, beep],

    components: {
        'pick-card': PickCard,
        'barcode-input-field': BarcodeInputField,
        'picklist-configuration-modal': PicklistConfigurationModalNew   ,
    },

    mounted() {
        if(this.getUrlParameter('inventory_source_location_id') === null) {
            this.setUrlParameter('inventory_source_location_id', 100);
        }
        this.reloadPicks();
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
            picklist: [],
            current_shelf_location: ''
        };
    },

    methods: {
        pickByBarcode(barcode) {
            if(barcode === '') {
                return;
            }

            let pickItem = this.findPickItem(barcode);

            if(pickItem) {
                this.pickAll(pickItem);
                return;
            }

            this.$snotify.error(`"${barcode}" not found on picklist!`);
            this.errorBeep();
        },

        findPickItem: function (barcode) {
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

            this.picklist = [];
            const params = {
                'include': 'product,product.aliases',
                'sort': 'inventory_source_shelf_location,sku_ordered',
                'per_page': this.getUrlParameter('per_page', 3),
                'filter[in_stock_only]': this.getUrlFilter('in_stock_only', true),
                'filter[inventory_source_location_id]': this.getUrlParameter('inventory_source_location_id'),
                'filter[current_shelf_location]': this.getUrlParameter('current_shelf_location'),
                'filter[order.status_code]': this.getUrlParameter('order.status_code'),
                'filter[created_between]': this.getUrlParameter('created_between'),
            };

            return axios.get('/api/picklist', {params:  params})
                .then( ({data}) => {
                    this.picklist = data.data;
                })
                .catch( error => {
                    this.$snotify.error('Action failed (Http code  '+ error.response.status+')');
                })
                .finally( () => {
                        this.hideLoading();
                        this.setFocusOnBarcodeInput();
                });
        },

        postPick(pick, quantity_picked, quantity_skipped_picking) {
            return axios.post('/api/picklist/picks', {
                    'quantity_picked': quantity_picked,
                    'quantity_skipped_picking': quantity_skipped_picking,
                    'order_product_ids': pick['order_product_ids'],
                })
                .catch( error => {
                    this.$snotify.error('Action failed (Http code  '+ error.response.status +')');
                });
        },

        postPickUpdate(pick, quantity_picked) {
            return axios.post('/api/picklist/picks', {
                    'quantity_picked': quantity_picked,
                    'order_product_ids': pick['order_product_ids'],
                })
                .catch( error => {
                    this.$snotify.error('Action failed (Http code  '+ error.response.status+')');
                });
        },

        removeFromPicklist: function (pick) {
            this.picklist.splice(this.picklist.indexOf(pick), 1);
        },

        pickAll(pick) {
            this.current_shelf_location = pick['inventory_source_shelf_location'];
            this.postPick(pick, pick['quantity_required'], 0)
                .then( () => {
                    this.displayPickedNotification(pick, pick['quantity_required']);
                    this.beep();
                    this.removeFromPicklist(pick);
                });
        },

        deletePick: function (pick) {
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
                            this.deletePick(pick);
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

        undoPick(pick, quantity) {
            this.showLoading();
            this.postPickUpdate(pick, -quantity)
                .then( () => {
                    this.reloadPicks()
                        .then(() => {
                            this.hideLoading();
                            this.$snotify.warning('Action reverted', {
                                timeout: 1500,
                                icon: false,
                            });
                            this.warningBeep();
                        });
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

<style scoped>

</style>
