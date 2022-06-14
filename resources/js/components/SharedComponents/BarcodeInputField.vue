<template>
    <div>
        <input class="form-control"
               :placeholder="placeholder"
               ref="barcode"
               id="barcodeInput"
               dusk="barcode-input-field"
               v-model="barcode"
               @focus="simulateSelectAll"
               @keyup.enter="barcodeScanned(barcode)"
        />


      <b-modal id="set-shelf-location-command-modal" @submit="updateShelfLocation" scrollable centered no-fade hide-header>
        <input id="set-shelf-location-command-modal-input" class="form-control" :placeholder="'Scan product to update shelf location: ' + command[1]"
               @focus="simulateSelectAll"
               @keyup.enter="updateShelfLocation"/>
      </b-modal>
    </div>
</template>

<script>
    import helpers from "../../mixins/helpers";
    import url from "../../mixins/url";
    import FiltersModal from "../Packlist/FiltersModal";
    import api from "../../mixins/api";

    export default {
        name: "BarcodeInputField",

        mixins: [helpers, url, FiltersModal, api],

        props: {
            url_param_name: null,
            placeholder: '',
        },

        data: function() {
            return {
                currentLocation: '',
                barcode: '',
                command: ['',''],
            }
        },

        mounted() {
            if(this.url_param_name) {
                this.barcode = this.getUrlParameter(this.url_param_name);
            }
            this.setFocusOnBarcodeInput();
        },

        methods: {
            hideModal(event) {
              this.$bvModal.hide('set-shelf-location-command-modal');
            },

            runCommandShelfScanned: function (command) {
              this.command = command;
              this.$bvModal.show('set-shelf-location-command-modal')
              this.warningBeep();
              this.setFocusElementById(1, 'set-shelf-location-command-modal-input')
            },

            tryToRunCommand: function (barcode) {
              let command = barcode.split(':');

              if(command.length < 2) {
                return false;
              }

              switch (command[0].toLowerCase())
              {
                  case 'shelf':
                    this.runCommandShelfScanned(command);
                    return true;
              }

              return false;
            },

            updateShelfLocation(event)
            {
              this.$bvModal.hide('set-shelf-location-command-modal');
              this.notifyError('Set Shelf Location command not yet implemented: ' + event.target.value);
              this.setFocusOnBarcodeInput();
            },

            barcodeScanned(barcode) {
                if(this.url_param_name) {
                    this.setUrlParameter(this.url_param_name, barcode);
                }

                if (this.tryToRunCommand(barcode)) {
                    this.barcode = '';
                    return;
                  }

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
