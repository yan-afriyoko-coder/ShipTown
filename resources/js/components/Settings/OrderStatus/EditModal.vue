<template>
    <div class="modal fade widget-configuration-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div ref="loadingContainer2" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Order Status</h5>
                </div>
                <div class="modal-body">
                    <ValidationObserver ref="form">
                        <form class="form" @submit.prevent="submit" ref="loadingContainer">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="reserves_stock">Reserves Stock</label>
                                <div class="col-sm-9">
                                    <ValidationProvider vid="reserves_stock" name="reserves_stock" v-slot="{ errors }">
                                        <div class="custom-control custom-switch mt-2" :class="{'is-invalid' : errors.length}">
                                            <input type="checkbox"
                                                id="edit-reserves_stock"
                                                class="custom-control-input"
                                                v-model="reservesStock"
                                                required>
                                            <label class="custom-control-label" for="edit-reserves_stock"></label>
                                        </div>
                                        <div class="invalid-feedback">
                                            {{ errors[0] }}
                                        </div>
                                    </ValidationProvider>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="order_active">Order Active</label>
                                <div class="col-sm-9">
                                    <ValidationProvider vid="order_active" name="order_active" v-slot="{ errors }">
                                        <div class="custom-control custom-switch mt-2" :class="{'is-invalid' : errors.length}">
                                            <input type="checkbox"
                                                id="edit-order_active"
                                                class="custom-control-input"
                                                v-model="orderActive"
                                                required>
                                            <label class="custom-control-label" for="edit-order_active"></label>
                                        </div>
                                        <div class="invalid-feedback">
                                            {{ errors[0] }}
                                        </div>
                                    </ValidationProvider>
                                </div>
                            </div>
                        </form>
                    </ValidationObserver>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="closeModal" class="btn btn-default">Cancel</button>
                    <button type="button" @click="submit" class="btn btn-primary">Ok</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ValidationObserver, ValidationProvider } from "vee-validate";

import Loading from "../../../mixins/loading-overlay";
import api from "../../../mixins/api";

export default {
    name: "EditModal",

    mixins: [api, Loading],

    components: {
        ValidationObserver, ValidationProvider
    },

    data() {
        return {
            reservesStock: false,
            orderActive: false,
        }
    },

    props: {
        orderStatus: Object,
    },

    watch: {
        orderStatus: function(newVal) {
            this.reservesStock = newVal.reserves_stock
            this.orderActive = newVal.order_active
        }
    },

    methods: {

        submit() {
            this.showLoading();
            this.apiPutOrderStatus(this.orderStatus.id, {
                    reserves_stock: this.reservesStock,
                    order_active: this.orderActive,
                })
                .then(({ data }) => {
                    this.$snotify.success('Order status updated.');
                    this.closeModal();
                    this.$emit('onUpdated', data.data);
                })
                .catch((error) => {
                    if (error.response) {
                        if (error.response.status === 422) {
                            this.$refs.form.setErrors(error.response.data.errors);
                        }
                    }
                })
                .finally(this.hideLoading);
        },

        closeModal() {
            $(this.$el).modal('hide');
        }
    },
}
</script>
