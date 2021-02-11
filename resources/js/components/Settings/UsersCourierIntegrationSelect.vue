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
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Automatically Print Label</td>
                            <td class="text-right">
                                <select class="w-100" @change="updateUsersAddressLabelTemplate" v-model="selected_address_label_template">
                                    <option value=""></option>
                                    <option value="address_label">address_label</option>
                                    <option value="dpd_label">dpd_label</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Ask to scan shipping number</td>
                            <td class="text-right">
                                <select class="w-100" @change="saveAskForShippingNumberValue" v-model="selected_ask_for_shipping_number">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
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

export default {
    mixins: [api],

    created() {
        this.apiGetUserMe()
            .then(({ data }) => {
                this.selected_address_label_template = data.data.address_label_template;
                this.selected_ask_for_shipping_number = data.data.ask_for_shipping_number === 'true';
            });
    },

    data: () => ({
        selected_address_label_template: "",
        selected_ask_for_shipping_number: false,
        error: false,
    }),

    methods: {
        updateUsersAddressLabelTemplate() {
            this.apiPostUserMe({
                'address_label_template': this.selected_address_label_template
            });
        },

        saveAskForShippingNumberValue() {
            this.apiPostUserMe({
                'ask_for_shipping_number': this.selected_ask_for_shipping_number
            });
        }
    }
}
</script>
