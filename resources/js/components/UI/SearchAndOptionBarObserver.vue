<template>
    <div ref="observerElement" style="height: 1px; visibility: hidden;"></div>
</template>

<script>
export default {
    mounted() {
        const observer = new IntersectionObserver((entries) => {
            const isVisible = entries[0].isIntersecting;
            this.$eventBus.$emit('observer-status', isVisible);
        }, {
            root: null,
            threshold: 0.1
        });

        observer.observe(this.$refs.observerElement);
    }
}
</script>
