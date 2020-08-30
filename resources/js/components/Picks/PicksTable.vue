<template>
    <div>
        <template v-for="pick in picklist">
            <pick-card :pick="pick" @swipeRight="pickAll" @swipeLeft="partialPickSwiped"/>
        </template>
    </div>
</template>

<script>
import PickCard from "./components/PickCard.vue";
import loadingOverlay from '../../mixins/loading-overlay';

export default {
    name: "PicksTable",

    mixins: [loadingOverlay],

    components: {
        'pick-card': PickCard
    },

    mounted() {
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
        }
    },

    data: function() {
        return {
            filters: {
                'filter[not_picked_only]': true,
            },
            picklist: [],
        };
    },

    methods: {
        reloadPicks() {
            this.showLoading();
            this.picklist = [];
            return axios.get('/api/picks', {params:  this.filters})
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
            this.postPickUpdate(pick, pick['quantity_required'])
                .then( () => {
                    this.displayPickedNotification(pick, pick['quantity_required']);
                });
        },

        deletePick: function (pick) {
            axios.delete('/api/picks/' + pick['id'])
                .then(() => {
                    this.$snotify.warning('Pick deleted');
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
