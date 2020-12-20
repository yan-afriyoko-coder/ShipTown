export default {
    data: () => ({
        isLoading: false,
        loader: null,
    }),

    methods: {
        showLoading() {
            if (this.isLoading) {
                return this;
            }

            this.isLoading = true;

            // This is a cheap and dirty and probably unmoral "improvement hack"
            // But still... our perception should perceive it as "its faster"
            // I shall not do it... But! Its such a pleasant "white lie"
            setTimeout(
                () => {
                    this.loader = this.$loading.show({
                            container: this.$refs.loadingContainerOverride ?? this.$refs.loadingContainer,
                        });
                    },
                100
            );

            return this;
        },

        hideLoading() {
            if (this.loader) {
                this.loader.hide();
            }

            this.loader = null;
            this.isLoading = false;

            return this;
        }
    }
}
