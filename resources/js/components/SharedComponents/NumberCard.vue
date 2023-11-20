<template>
    <text-card :label="label" :text="numberFormatted"></text-card>
</template>

<script>
    import helpers from "../../mixins/helpers";

    export default {
        mixins: [helpers],
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
                return this.toNumberOrDash(Number(this.number || 0));
            },
        },
    }
</script>

<style scoped>

</style>
