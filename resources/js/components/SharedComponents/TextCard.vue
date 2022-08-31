<template>
    <div class="text-center border-0 d-inline-block p-2" style="min-width: 70px">
            <small>{{ this.label }}</small>
            <h3>{{ this.text || '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'}}</h3>
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
