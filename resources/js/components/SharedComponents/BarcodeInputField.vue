<template>
    <div>
        <input ref="barcode" id="barcodeInput" class="form-control" :placeholder="placeholder"
               v-model="barcode"
               @focus="simulateSelectAll"
               @keyup.enter="barcodeScanned(barcode)"/>
    </div>
</template>

<script>
    import helpers from "../../mixins/helpers";

    export default {
        name: "BarcodeInputField",

        mixins: [helpers],

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
            this.setFocusOnBarcodeInput(500);
        },

        methods: {
            barcodeScanned(barcode) {
                this.$emit('barcodeScanned', barcode);
                this.setFocusOnBarcodeInput();
                this.simulateSelectAll();
            },

            setFocusOnBarcodeInput(delay = 1) {
                setTimeout(() => {
                    this.setFocus(document.getElementById('barcodeInput'), true,true)
                }, delay);
            },

            simulateSelectAll() {
                setTimeout(() => {
                    document.execCommand('selectall', null, false);
                }, 1);
            },

        }
    }
</script>

<style scoped>

</style>
