<template>
    <div>
        <input ref="barcode" id="barcodeInput" class="form-control" :placeholder="placeholder"
               v-model="barcode"
               @focus="simulateSelectAll"
               @keyup.enter="barcodeScanned(barcode)"/>


      <b-modal id="set-shelf-location-command-modal" @submit="updateShelfLocation" scrollable centered no-fade hide-header>
        <input ref="barcode" id="set-shelf-location-command-modal-input" class="form-control" :placeholder="'Scan product to update shelf location: ' + command[1]"
               @focus="simulateSelectAll"
               @keyup.enter="updateShelfLocation"/>
      </b-modal>
    </div>
</template>

<script>
    import helpers from "../../mixins/helpers";
    import url from "../../mixins/url";
    import FiltersModal from "../Packlist/FiltersModal";

    export default {
        name: "BarcodeInputField",

        mixins: [helpers, url, FiltersModal],

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
            this.barcode = this.getUrlParameter(this.url_param_name);
            this.setFocusOnBarcodeInput(500);
        },

        methods: {
          hideModal(event) {
            this.$bvModal.hide('set-shelf-location-command-modal');
          },

          runCommandShelfScanned: function (command) {
            this.command = command;
            this.warningBeep();
            this.$bvModal.show('set-shelf-location-command-modal')
            this.setFocusElementById(500, 'set-shelf-location-command-modal-input')
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
                if (this.tryToRunCommand(barcode)) {
                  this.barcode = '';
                  return;
                }

                this.$emit('barcodeScanned', barcode);
                if(this.url_param_name) {
                  this.setUrlParameter(this.url_param_name, barcode);
                }
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
