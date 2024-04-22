<template>
    <button type="button" class="btn btn-primary ml-2">
        <font-awesome-icon :icon="icon" class="fa-lg"></font-awesome-icon>
    </button>
</template>

<script>

export default {
    props: {
        icon: {
            type: String,
            default: 'cog'
        }
    }
}
</script>
