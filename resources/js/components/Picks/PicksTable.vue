<template>
    <div>
        <div class="row mb-3">
            <div class="col-8 pl-1 pr-1">
                <barcode-input-field @barcodeScanned="pickByBarcode"/>
            </div>
            <div class="col-4 pr-2">
                <div class="">
                    <input ref="current_location" class="form-control" placeholder="Current shelf"
                           v-model="current_shelf_location"
                           @keyup.enter="reloadPicks()"/>
                </div>

            </div>
        </div>
        <div>
            <template v-for="pick in picklist">
                <pick-card :pick="pick" @swipeRight="pickAll" @swipeLeft="partialPickSwiped"/>
            </template>
        </div>
    </div>
</template>

<script>
import PickCard from "./components/PickCard.vue";
import loadingOverlay from '../../mixins/loading-overlay';
import BarcodeInputField from "../SharedComponents/BarcodeInputField";

import url from "../../mixins/url";
import beep from "../../mixins/beep";

export default {
    name: "PicksTable",

    mixins: [loadingOverlay, url, beep],

    components: {
        'pick-card': PickCard,
        'barcode-input-field': BarcodeInputField,
    },

    mounted() {
        this.reloadPicks();
        this.setUrlFilter('inventory_source_id', 100);
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
                this.setFocusOnBarcodeInput();
                this.simulateSelectAll();
                return;
            }

            this.$snotify.error(`"${barcode}" not found on picklist!`);
            this.setFocusOnBarcodeInput();
            this.simulateSelectAll();
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
                sort: 'inventory_source_shelf_location',
                per_page: 3,
                'filter[not_picked_only]': true,
                'filter[inventory_source_id]': this.getUrlFilter('inventory_source_id'),
                'filter[current_shelf_location]': this.getUrlFilter('current_shelf_location'),
            };

            this.showLoading();

            this.picklist = [];

            return axios.get('/api/picks', {params:  params})
                .then( ({data}) => {
                    this.picklist = data.data;
                })
                .catch( error => {
                    this.$snotify.error('Action failed (Http code  '+ error.response.status+')');
                })
                .then( () => {
                        this.hideLoading();
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
            this.removeFromPicklist(pick);
            this.current_shelf_location = pick['inventory_source_shelf_location'];
            this.postPickUpdate(pick, pick['quantity_required'])
                .then( () => {
                    this.displayPickedNotification(pick, pick['quantity_required']);
                    this.beep();
                });
        },

        deletePick: function (pick) {
            axios.delete('/api/picks/' + pick['id'])
                .then(() => {
                    this.$snotify.warning('Pick deleted');
                    this.warningBeep();
                })
                .catch( error => {
                    this.$snotify.error('Action failed (Http code  '+ error.response.status+')');
                })
                .then(() => {
                    this.reloadPicks();
                })
        },

        makePartialPick: function (pick, toast) {
            this.removeFromPicklist(pick);
            this.postPickUpdate(pick, toast.value)
                .then(() => {
                    this.displayPickedNotification(pick, pick['quantity_required']);
                    this.beep();
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
                            this.makePartialPick(pick, toast);
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
                            this.$snotify.warning('Action reverted');
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
