<template>
    <div>
        <input ref="barcode" class="form-control" placeholder="Scan sku or barcode"
               v-observe-visibility="barcodeVisibilityChanged"
               v-model="barcode"
               @focus="simulateSelectAll"
               @keyup.enter="pickBarcode(barcode)"/>
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
            }
        },

        methods: {
            barcodeVisibilityChanged: function() {
                this.setFocusOnBarcodeInput();
                this.simulateSelectAll();
            },

            setFocusOnBarcodeInput() {
                this.$refs.barcode.focus();
            },

            simulateSelectAll() {
                setTimeout(() => { document.execCommand('selectall', null, false); });
            },

        }
    }
</script>

<style scoped>

</style>
