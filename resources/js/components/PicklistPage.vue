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
                <pick-card :pick="pick" @swipeRight="pickAll" @swipeLeft="partialPickSwiped"/>
            </template>
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
                 if (this.isLoading) {
                     return;
                 }

                if (this.picklist.length === 0) {
                    this.reloadPicks();
                }

            },
            deep: true
        },
        current_shelf_location() {
            this.setUrlFilter('current_shelf_location', this.current_shelf_location);
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
            const params = {
                include: 'product,product.aliases',
                sort: 'inventory_source_shelf_location,sku_ordered',
                per_page: this.getUrlParameter('per_page', 3),
                'filter[in_stock_only]': this.getUrlFilter('in_stock_only', true),
                'filter[inventory_source_location_id]': this.getUrlParameter('inventory_source_location_id'),
                'filter[current_shelf_location]': this.getUrlFilter('current_shelf_location'),
            };

            this.showLoading();

            this.picklist = [];

            return axios.get('/api/order/picklist', {params:  params})
                .then( ({data}) => {
                    this.picklist = data.data;
                })
                .catch( error => {
                    this.$snotify.error('Action failed (Http code  '+ error.response.status+')');
                })
                .then( () => {
                        this.hideLoading();
                        this.setFocusOnBarcodeInput();
                });
        },

        postPickUpdate(pick, quantity_picked) {
            return axios.put('/api/picks/' + pick['id'], {
                    'quantity_picked': quantity_picked
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
            this.postPickUpdate(pick, pick['quantity_required'])
                .then( () => {
                    this.displayPickedNotification(pick, pick['quantity_required']);
                    this.beep();
                    this.removeFromPicklist(pick);
                });
        },

        deletePick: function (pick) {
            axios.delete('/api/picks/' + pick['id'])
                .then(() => {
                    this.$snotify.warning('Pick deleted', {
                        timeout: 1500,
                        icon: false,
                    });
                    this.warningBeep();
                })
                .catch( error => {
                    this.$snotify.error('Action failed (Http code  '+ error.response.status+')');
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

        undoPick(pick) {
            this.showLoading();
            this.postPickUpdate(pick,0)
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
                            this.undoPick(pick)
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
