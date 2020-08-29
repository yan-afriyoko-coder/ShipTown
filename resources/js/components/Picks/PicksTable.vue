<template>
    <div>
        <template v-for="pick in picklist">
            <pick-card :pick="pick" @swipeRight="pickAll" @swipeLeft="pickPartial"/>
        </template>
    </div>
</template>

<script>
import PickCard from "./components/PickCard.vue";

export default {
    name: "PicksTable",

    components: {
        'pick-card': PickCard
    },

    mounted() {
        this.loadPicks();
    },

    watch: {
        picklist: {
            handler() {
                if (this.picklist.length === 0) {
                    this.loadPicks();
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
        loadPicks() {
            return axios.get('/api/picks', {})
                .then( ({data}) => {
                    this.picklist = data.data;
                })
        },

        pickPick(pick, quantity) {
            return axios.put('/api/picks/' + pick['id'], {
                'quantity_picked': quantity
            });
        },

        removeFromPicklist: function (pick) {
            this.picklist.splice(this.picklist.indexOf(pick), 1);
        },

        pickAll(pick) {
            this.removeFromPicklist(pick);
            this.pickPick(pick, pick['quantity_required'])
                .then( () => {
                    this.displayPickedNotification(pick, pick['quantity_required']);
                });
        },

        pickPartial(pick) {
            console.log('pickPartial',pick);
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
                            this.pickPick(pick,0);
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
