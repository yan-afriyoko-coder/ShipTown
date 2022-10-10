<template>
    <div class="text-center border-0 d-inline-block p-2" style="min-width: 70px">
            <small class="text-secondary">{{ this.label }}</small>
            <h4>{{ this.text || '&nbsp;'}}</h4>
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
