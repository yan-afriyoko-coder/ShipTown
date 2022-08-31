<template>
    <div class="text-center border-0 d-inline-block p-2">
            <small>{{ this.label }}</small>
            <h3>{{ this.text }}</h3>
    </div>
</template>

<script>
    export default {
        name: "TextCard",

        props: {
            label: null,
            text: null,
        },
    }
</script>

<style scoped>

</style>
