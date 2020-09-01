<template>
    <div>
        <input ref="barcode" class="form-control" placeholder="Scan sku or barcode"
               v-observe-visibility="setFocusOnBarcodeInput"
               v-model="barcode"
               @focus="simulateSelectAll"
               @keyup.enter="barcodeScanned(barcode)"/>
    </div>
</template>

<script>
    import VueObserveVisibilityPlugin from 'vue-observe-visibility';

    export default {
        name: "BarcodeInputField",

        data: function() {
            return {
                currentLocation: '',
                barcode: '',
                placeholder: 'Scan sku 2or barcode'
            }
        },

        methods: {
            barcodeScanned(barcode) {
                this.$emit('barcodeScanned', barcode);
                this.setFocusOnBarcodeInput();
            },

            setFocusOnBarcodeInput() {
                if (this.$refs.barcode === document.activeElement) {
                    return;
                }

                this.$refs.barcode.focus();
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
