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
            axios.get('/api/picks', {})
                .then( ({data}) => {
                    this.picklist = data.data;
                })
        },

        removeFromPicklist: function (pick) {
            this.picklist.splice(this.picklist.indexOf(pick), 1);
        },

        pickAll(pick) {
            console.log('pickAll', pick);
            this.removeFromPicklist(pick);
        },

        pickPartial(pick) {
            console.log('pickPartial',pick);
        }

    },
}
</script>

<style scoped>

</style>
