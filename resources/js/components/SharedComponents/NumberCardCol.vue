<template>
    <div class="text-center border-0 d-inline-block p-2">
            <small>{{ this.labelFormatted }}</small>
            <h3>{{ this.numberFormatted }}</h3>
    </div>
</template>

<script>
    export default {
        name: "NumberCardCol",

        props: {
            label: null,
            number: null,
        },

        computed: {
            labelFormatted() {
                return this.label || '';
            },
            numberFormatted() {
                return this.dashIfZero(Number(this.number || 0));
            },
        },

        methods: {
            dashIfZero(value) {
                return value === 0 ? '-' : value;
            },
        },

        data: function() {
            return {

            }
        },
    }
</script>

<style scoped>

</style>
