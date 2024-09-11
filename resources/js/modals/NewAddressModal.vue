<template>
    <b-modal body-class="ml-0 mr-0 pl-1 pr-1" :id="modalId" title="Select or create new shipping/billing address"
             size="xl" scrollable no-fade>
        <template #modal-header>
            <span>New Product</span>
        </template>

        <div class="container">
            <ValidationObserver v-slot="{ handleSubmit }">
                <form @submit.prevent="handleSubmit(createNewAddress)" ref="loadingContainer" class="addressForm">
                    <div class="d-flex align-items-center addressForm__wrapper">
                        <div class="form-group addressForm__item">
                            <label class="form-label" for="newAddressFirstName">First Name</label>
                            <ValidationProvider vid="newAddressFirstName" name="newAddressFirstName"
                                                v-slot="{ errors }">
                                <input v-model="newAddress.first_name" type="text" :disabled="!isCreatingAddress"
                                       :class="{'form-control': true,'is-invalid': errors.length > 0}"
                                       id="newAddressFirstName" placeholder="John" required>
                                <div class="invalid-feedback">
                                    {{ errors[0] }}
                                </div>
                            </ValidationProvider>
                        </div>
                        <div class="form-group addressForm__item">
                            <label class="form-label" for="newAddressLastName">Last Name</label>
                            <ValidationProvider vid="newAddressLastName" name="newAddressLastName" v-slot="{ errors }">
                                <input v-model="newAddress.last_name" type="text" :disabled="!isCreatingAddress"
                                       :class="{'form-control': true,'is-invalid': errors.length > 0}"
                                       id="newAddressLastName" placeholder="Doe" required>
                                <div class="invalid-feedback">
                                    {{ errors[0] }}
                                </div>
                            </ValidationProvider>
                        </div>
                        <div class="form-group addressForm__item">
                            <label class="form-label" for="newAddressGender">Gender</label>
                            <ValidationProvider vid="newAddressGender" name="newAddressGender" v-slot="{ errors }">
                                <select v-model="newAddress.gender" name="newAddressGender" id="newAddressGender"
                                        :disabled="!isCreatingAddress"
                                        :class="{'form-control': true,'is-invalid': errors.length > 0}" required>
                                    <option value="" disabled selected>Gender</option>
                                    <option v-for="(gender, index) in genders" :value="gender" :key="`type${index}`">
                                        {{ gender }}
                                    </option>
                                </select>
                                <div class="invalid-feedback">
                                    {{ errors[0] }}
                                </div>
                            </ValidationProvider>
                        </div>
                        <div class="form-group addressForm__item">
                            <label class="form-label" for="newAddressFirstLine">Address Line 1</label>
                            <ValidationProvider vid="newAddressFirstLine" name="newAddressFirstLine"
                                                v-slot="{ errors }">
                                <input v-model="newAddress.address1" type="text" :disabled="!isCreatingAddress"
                                       :class="{'form-control': true,'is-invalid': errors.length > 0}"
                                       id="newAddressFirstLine" placeholder="215 E Tasman Dr" required>
                                <div class="invalid-feedback">
                                    {{ errors[0] }}
                                </div>
                            </ValidationProvider>
                        </div>
                        <div class="form-group addressForm__item">
                            <label class="form-label" for="newAddressSecondLine">Address Line 2</label>
                            <ValidationProvider vid="newAddressSecondLine" name="newAddressSecondLine"
                                                v-slot="{ errors }">
                                <input v-model="newAddress.address2" type="text" :disabled="!isCreatingAddress"
                                       :class="{'form-control': true,'is-invalid': errors.length > 0}"
                                       id="newAddressSecondLine" placeholder="Po Box 65502" required>
                                <div class="invalid-feedback">
                                    {{ errors[0] }}
                                </div>
                            </ValidationProvider>
                        </div>
                        <div class="form-group addressForm__item">
                            <label class="form-label" for="newAddressPostCode">Post Code</label>
                            <ValidationProvider vid="newAddressPostCode" name="newAddressPostCode" v-slot="{ errors }">
                                <input v-model="newAddress.postcode" type="text" :disabled="!isCreatingAddress"
                                       :class="{'form-control': true,'is-invalid': errors.length > 0}"
                                       id="newAddressPostCode" placeholder="95132" required>
                                <div class="invalid-feedback">
                                    {{ errors[0] }}
                                </div>
                            </ValidationProvider>
                        </div>
                        <div class="form-group addressForm__item">
                            <label class="form-label" for="newAddressCity">City</label>
                            <ValidationProvider vid="newAddressCity" name="newAddressCity" v-slot="{ errors }">
                                <input v-model="newAddress.city" type="text" :disabled="!isCreatingAddress"
                                       :class="{'form-control': true,'is-invalid': errors.length > 0}"
                                       id="newAddressCity" placeholder="San Jose" required>
                                <div class="invalid-feedback">
                                    {{ errors[0] }}
                                </div>
                            </ValidationProvider>
                        </div>
                        <div class="form-group addressForm__item">
                            <label class="form-label" for="newAddressCountryCode">Country Code</label>
                            <ValidationProvider vid="newAddressCountryCode" name="newAddressCountryCode"
                                                v-slot="{ errors }">
                                <select v-model="newAddress.country_code" name="newAddressCountryCode"
                                        id="newAddressCountryCode"
                                        :disabled="!isCreatingAddress"
                                        :class="{'form-control': true,'is-invalid': errors.length > 0}" required>
                                    <option value="" selected disabled>Select an option</option>
                                    <option value="IE">IE</option>
                                    <option value="IRL">IRL</option>
                                    <option value="UK">UK</option>
                                    <option value="GB">GB</option>
                                </select>
                                <div class="invalid-feedback">
                                    {{ errors[0] }}
                                </div>
                            </ValidationProvider>
                        </div>
                        <div class="form-group addressForm__item">
                            <label class="form-label" for="newAddressCompanyName">Company Name</label>
                            <ValidationProvider vid="newAddressCompanyName" name="newAddressCompanyName"
                                                v-slot="{ errors }">
                                <input v-model="newAddress.company" type="text" :disabled="!isCreatingAddress"
                                       :class="{'form-control': true,'is-invalid': errors.length > 0}"
                                       id="newAddressCompanyName" placeholder="Confidential LTD." required>
                                <div class="invalid-feedback">
                                    {{ errors[0] }}
                                </div>
                            </ValidationProvider>
                        </div>
                        <div class="form-group addressForm__item">
                            <label class="form-label" for="newAddressEmail">Email</label>
                            <ValidationProvider vid="newAddressEmail" name="newAddressEmail" v-slot="{ errors }">
                                <input v-model="newAddress.email" type="email" :disabled="!isCreatingAddress"
                                       :class="{'form-control': true,'is-invalid': errors.length > 0}"
                                       id="newAddressEmail" placeholder="john@example.com" required>
                                <div class="invalid-feedback">
                                    {{ errors[0] }}
                                </div>
                            </ValidationProvider>
                        </div>
                        <div class="form-group addressForm__item">
                            <label class="form-label" for="newAddressPhoneNumber">Phone</label>
                            <ValidationProvider vid="newAddressPhoneNumber" name="newAddressPhoneNumber"
                                                v-slot="{ errors }">
                                <input v-model="newAddress.phone" type="text" :disabled="!isCreatingAddress"
                                       :class="{'form-control': true,'is-invalid': errors.length > 0}"
                                       id="newAddressPhoneNumber" placeholder="+353 1 344 1111" required>
                                <div class="invalid-feedback">
                                    {{ errors[0] }}
                                </div>
                            </ValidationProvider>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <b-button variant="secondary" class="mr-2" @click="closeModal">Cancel</b-button>
                        <b-button variant="primary" type="submit">Create</b-button>
                    </div>
                </form>
            </ValidationObserver>
        </div>
        <template #modal-footer>
            <div></div>
        </template>
    </b-modal>
</template>

<script>

import ProductCard from "../components/Products/ProductCard.vue";
import api from "../mixins/api.vue";
import Modals from "../plugins/Modals";
import {ValidationObserver, ValidationProvider} from "vee-validate";
import loadingOverlay from "../mixins/loading-overlay";

export default {
    components: {ProductCard, ValidationObserver, ValidationProvider},

    mixins: [api, loadingOverlay],

    beforeMount() {
        Modals.EventBus.$on('show::modal::' + this.modalId, (data) => {
            this.address = data['address'];

            this.newAddress = {
                first_name: '',
                last_name: '',
                gender: '',
                address1: '',
                address2: '',
                postcode: '',
                city: '',
                country_code: '',
                company: '',
                email: '',
                phone: '',
            };

            if (this.address) {
                this.newAddress.first_name = this.address.first_name;
                this.newAddress.last_name = this.address.last_name;
                this.newAddress.gender = this.address.gender;
                this.newAddress.address1 = this.address.address1;
                this.newAddress.address2 = this.address.address2;
                this.newAddress.postcode = this.address.postcode;
                this.newAddress.city = this.address.city;
                this.newAddress.country_code = this.address.country_code;
                this.newAddress.company = this.address.company;
                this.newAddress.email = this.address.email;
                this.newAddress.phone = this.address.phone;
            }

            this.$bvModal.show(this.modalId);
        })
    },

    data() {
        return {
            newAddress: {
                first_name: '',
                last_name: '',
                gender: '',
                address1: '',
                address2: '',
                postcode: '',
                city: '',
                country_code: '',
                company: '',
                email: '',
                phone: '',
            },
            genders: ['Ms.', 'Mr.', 'Mrs.', 'Dr.', 'Prof.'],
            modalId: 'new-address-modal',
            address: undefined
        }
    },

    computed: {
        isCreatingAddress() {
            return this.address === null || (this.address === undefined);
        }
    },

    methods: {
        createNewAddress() {
            this.showLoading();
            this.apiPostAddress(this.newAddress)
                .then(response => {
                    this.$bvModal.hide(this.modalId);
                    Modals.EventBus.$emit(`hide::modal::${this.modalId}`, {
                        address: response.data.data,
                        addressSaved: true
                    });
                })
                .catch(error => {
                    this.displayApiCallError(error);
                })
                .finally(() => {
                    this.hideLoading();
                })
        },

        closeModal() {
            this.$bvModal.hide(this.modalId);
            Modals.EventBus.$emit(`hide::modal::${this.modalId}`, {
                addressSaved: false
            });
        }
    },

    watch: {
        'newAddress.country_code': function (newVal) {
            this.newAddress.country_name = newVal === 'IE' || newVal === 'IRL' ? 'Ireland' : 'United Kingdom';
        }
    }
};

</script>

<style lang="scss" scoped>
.addressForm {
    &__wrapper {
        flex-wrap: wrap;
        gap: 10px;
    }

    &__item {
        flex: 0 0 calc(50% - 10px + (10px / 2));
        max-width: calc(50% - 10px + (10px / 2));
    }
}

@media all and (max-width: 768px) {
    .addressForm {
        &__item {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
}
</style>
