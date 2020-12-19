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
