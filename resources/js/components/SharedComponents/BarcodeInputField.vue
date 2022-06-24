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
            this.resetInputValue();
            this.setFocusOnBarcodeInput();
        },

        methods: {
            resetInputValue: function () {
                if (this.url_param_name) {
                    this.barcode = this.getUrlParameter(this.url_param_name);
                }
            },

            hideModal(event) {
                this.$bvModal.hide('set-shelf-location-command-modal');
            },

            runCommandShelfScanned: function () {
                document.getElementById('set-shelf-location-command-modal-input').readOnly = true;

                this.$bvModal.show('set-shelf-location-command-modal')
                this.warningBeep();
                this.setFocusElementById(1, 'set-shelf-location-command-modal-input')
            },

            tryToRunCommand: function (barcode) {
                let command = barcode.split(':');

                if(command.length < 2) {
                    return false;
                }

                this.command['name'] = command[0];
                this.command['value'] = command[1];

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

                this.apiGetInventory({
                          'filter[sku_or_alias]': event.target.value,
                          'filter[warehouse_id]': this.currentUser()['warehouse_id'],
                    })
                    .then((response) => {
                        if (response.data['meta']['total'] !== 1) {
                            this.notifyError('SKU "'+ event.target.value +'" not found ');
                            return;
                        }

                        const inventory = response.data.data[0];
                        this.apiPostInventory({
                                'id': inventory['id'],
                                'shelve_location': this.command['value'],
                            })
                            .then(() => {
                                this.notifySuccess('Shelf updated');
                            })
                            .catch((error) => {
                                this.displayApiCallError(error)
                            });
                    })
                    .catch((error) => {
                        this.displayApiCallError(error)
                    });

                this.resetInputValue();
                this.setFocusOnBarcodeInput();
            },

            barcodeScanned(barcode) {
                if (this.tryToRunCommand(barcode)) {
                    this.barcode = '';
                    return;
                  }

                if(this.url_param_name) {
                    this.setUrlParameter(this.url_param_name, barcode);
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
