<template>
    <div>
        <template v-for="pick in picklist">
            <pick-card :pick="pick" @swipeRight="pickAll" @swipeLeft="pickPartial"/>
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
            picklist: [],
        };
    },

    methods: {
        reloadPicks() {
            this.showLoading();
            this.picklist = [];
            return axios.get('/api/picks', {})
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

        pickPartial(pick) {
            console.log('pickPartial', pick);
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
                            this.showLoading();
                            this.postPickUpdate(pick,0)
                                .then( () => {
                                    this.reloadPicks().then(() => {
                                        this.hideLoading();
                                    });
                                });
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
