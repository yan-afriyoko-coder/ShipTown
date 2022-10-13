<template>
    <div class="text-center border-0 d-inline-block p-0 m-0" style="min-width: 70px">
            <div class="small text-secondary">
                <small class="text-secondary small">{{ this.label }}</small>
            </div>
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
