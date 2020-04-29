export default {
    data: () => ({
        isLoading: false,
        loader: null,
    }),
    
    methods: {
        showLoading() {
            this.isLoading = true;

            this.loader = this.$loading.show({
                container: this.$refs.loadingContainer,
            });
        },
        
        hideLoading() {
            if (this.loader) {
                this.loader.hide();
            }
            
            this.loader = null;
            this.isLoading = false;
        }
    }
}