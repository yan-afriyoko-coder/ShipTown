<template>
    <b-modal id="modal-create-connection"
        title="Add Connection"
        ok-title="Save"
        @ok="submit"
    >
        <ValidationObserver ref="form">
            <form class="form" @submit.prevent="submit" ref="loadingContainer">

                <div class="form-group">
                    <label class="form-label" for="base_url">Base URL</label>
                    <ValidationProvider vid="base_url" name="base_url" v-slot="{ errors }">
                    <input v-model="config.base_url" :class="{
                                'form-control': true,
                                'is-invalid': errors.length > 0,
                            }" id="create-base_url" type="url" required>
                    <div class="invalid-feedback">
                        {{ errors[0] }}
                    </div>
                    </ValidationProvider>
                </div>

                <div class="form-group">
                    <label class="form-label" for="magento_store_id">Store ID</label>
                    <ValidationProvider vid="magento_store_id" name="magento_store_id" v-slot="{ errors }">
                        <input v-model="config.magento_store_id" :class="{
                            'form-control': true,
                            'is-invalid': errors.length > 0,
                        }" id="create-magento_store_id" type="number" required>
                        <div class="invalid-feedback">
                            {{ errors[0] }}
                        </div>
                    </ValidationProvider>
                </div>

                <div class="form-group">
                    <label class="form-label" for="tag">Inventory source warehouse tag</label>
                    <ValidationProvider vid="tag" name="tag" v-slot="{ errors }">
                        <input v-model="config.tag" :class="{
                            'form-control': true,
                            'is-invalid': errors.length > 0,
                        }" id="create-tag" required>
                        <div class="invalid-feedback">
                            {{ errors[0] }}
                        </div>
                    </ValidationProvider>
                </div>

                <div class="form-group">
                    <label class="form-label" for="pricing_source_warehouse_id">Pricing source warehouse</label>
                    <ValidationProvider vid="pricing_source_warehouse_id" name="pricing_source_warehouse_id" v-slot="{ errors }">
                        <select v-model="config.pricing_source_warehouse_id" :class="{
                            'form-control': true,
                            'is-invalid': errors.length > 0,
                        }" id="create-pricing_source_warehouse_id" required>
                            <option v-for="warehouse in warehouses"
                                :value="warehouse.id" :key="warehouse.id"
                            >
                                {{ warehouse.name }}
                            </option>
                        </select>
                        <div class="invalid-feedback">
                            {{ errors[0] }}
                        </div>
                    </ValidationProvider>
                </div>

                <div class="form-group">
                    <label class="form-label" for="access_token_encrypted">Access Token</label>
                    <ValidationProvider vid="access_token_encrypted" name="access_token_encrypted" v-slot="{ errors }">
                        <input v-model="config.access_token_encrypted" :class="{
                            'form-control': true,
                            'is-invalid': errors.length > 0,
                        }" id="create-access_token_encrypted" required>
                        <div class="invalid-feedback">
                            {{ errors[0] }}
                        </div>
                    </ValidationProvider>
                </div>
            </form>
        </ValidationObserver>

        <template #modal-footer>
            <b-button
                variant="secondary"
                class="float-right"
                @click="closeModal"
            >
                Cancel
            </b-button>
            <b-button
                variant="primary"
                class="float-right"
                @click="submit"
            >
                Save
            </b-button>
        </template>
    </b-modal>
</template>

<script>
import { ValidationObserver, ValidationProvider } from "vee-validate";

import Loading from "../../../mixins/loading-overlay";
import api from "../../../mixins/api";

export default {
    name: "CreateModal",

    mixins: [api, Loading],

    components: {
        ValidationObserver, ValidationProvider
    },

    mounted() {
        this.fetchWarehouses()
    },

    data() {
        return {
            config: {
                base_url: location.protocol + '//' + location.host
            },
            warehouses: []
        }
    },

    methods: {
        fetchWarehouses: function () {
            this.apiGetWarehouses({
                'per_page': 100,
                'sort': 'code',
                'include': 'tags'
            })
                .then(({data}) => {
                    this.warehouses = data.data;
                })
        },

        submit() {
            this.showLoading();
            this.apiPostMagentoApiConnection({...this.config})
                .then(({ data }) => {
                    this.$snotify.success('Connection created.');
                    this.resetForm()
                    this.closeModal()
                    this.$emit('onCreated');
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

        resetForm(){
            this.config = {}
        },

        closeModal() {
            this.$bvModal.hide('modal-create-connection')
        }
    },
}
</script>
