<template>
    <div>
        <input ref="barcode" class="form-control" :placeholder="placeholder"
               v-model="barcode"
               @focus="simulateSelectAll"
               @keyup.enter="barcodeScanned(barcode)"/>
    </div>
</template>

<script>
    import VueObserveVisibilityPlugin from 'vue-observe-visibility';

    export default {
        name: "BarcodeInputField",

        props: {
            placeholder: '',
        },

        data: function() {
            return {
                currentLocation: '',
                barcode: '',
            }
        },

        mounted() {
            setTimeout(() => { this.setFocusOnBarcodeInput() }, 500);
        },

        methods: {
            barcodeScanned(barcode) {
                this.$emit('barcodeScanned', barcode);
                this.setFocusOnBarcodeInput();
                this.simulateSelectAll();
            },

            setFocusOnBarcodeInput() {

                if (this.$refs.barcode === document.activeElement) {
                    return;
                }

                // we using readOnly field here to prevent
                // on-screen keyboard showing up
                // on phones and tablets
                this.$refs.barcode.readOnly = true;
                this.$refs.barcode.focus();
                this.$refs.barcode.readOnly = false;

                this.simulateSelectAll();
            },

            simulateSelectAll() {
                setTimeout(() => { document.execCommand('selectall', null, false); });
            },

        }
    }
</script>

<style scoped>

</style>
