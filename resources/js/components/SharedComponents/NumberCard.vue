<template>
    <text-card :label="label" :text="numberFormatted"></text-card>
</template>

<script>
    export default {
        name: "NumberCard",

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
    }
</script>

<style scoped>

</style>
