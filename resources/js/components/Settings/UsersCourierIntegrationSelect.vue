<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Shipping Labels Configuration
                    </span>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-responsive table-borderless">
                    <tbody>
                        <tr>
                            <td>When last item scanned, Automatically Print:</td>
                            <td class="text-right">
                                <select class="form-control w-100" @change="updateUsersAddressLabelTemplate" v-model="selected_address_label_template">
                                    <option value=""></option>
                                    <option value="address_label">address_label</option>
                                    <option value="dpd_label">dpd_label</option>
                                    <option value="dpd_uk">dpd_uk_label</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Ask to scan shipping number</td>
                            <td class="text-right">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" @change="saveAskForShippingNumberValue" class="custom-control-input" id="swicth-scan-shipping-number" v-model="selected_ask_for_shipping_number">
                                    <label class="custom-control-label" for="swicth-scan-shipping-number"></label>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import api from "../../mixins/api";
import Vue from "vue";
import helpers from "../../mixins/helpers";

export default {
    mixins: [api, helpers],

    mounted() {
        this.selected_address_label_template = Vue.prototype.$currentUser.address_label_template;
        this.selected_ask_for_shipping_number = Vue.prototype.$currentUser.ask_for_shipping_number;
    },

    data: () => ({
        selected_address_label_template: "",
        selected_ask_for_shipping_number: false,
    }),

    methods: {
        updateUsersAddressLabelTemplate() {
            this.apiPostUserMe({
                    'address_label_template': this.selected_address_label_template
                })
                .catch(e => {
                    this.showException(e);
                });
        },

        saveAskForShippingNumberValue() {
            this.apiPostUserMe({
                    'ask_for_shipping_number': this.selected_ask_for_shipping_number
                })
                .catch(e => {
                    this.showError('Request failed: ' + e.message);
                });
        }
    }
}
</script>
