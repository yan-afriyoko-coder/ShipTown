<template>
    <div ref="form">
        <form class="form" @submit.prevent="submit" ref="loadingContainer">
            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="account_number">Account Number</label>
                <div class="col-sm-9">
                    <input v-model="connection['account_number']" class="form-control" id='account_number' placeholder="Account Number" required>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="username">Username</label>
                <div class="col-sm-9">
                    <input type="text" v-model="connection['username']" class="form-control" id="username" placeholder="Username" required>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="password">Password</label>
                <div class="col-sm-9">
                    <input type="password" v-model="connection['password']" class="form-control" id="password" placeholder="Password" required>
                </div>
            </div>

            <div class="row h5 mt-5">Collection Address</div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="contact_name">Contact Name</label>
                <div class="col-sm-9">
                    <input v-model="collectionAddress['full_name']" class="form-control" id="contact_name" placeholder="Contact Name" required>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3  col-form-label" for="company">Business Name</label>
                <div class="col-sm-9 ">
                    <input v-model="collectionAddress['company']" class="form-control" id="company" placeholder="Company Name"/>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3  col-form-label" for="email">Contact Email</label>
                <div class="col-sm-9 ">
                    <input type="email" v-model="collectionAddress['email']" class="form-control" id="email" placeholder="Contact Email"/>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3  col-form-label" for="phone">Telephone</label>
                <div class="col-sm-9 ">
                    <input v-model="collectionAddress['phone']" class="form-control" id="phone" placeholder="Telephone" required>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3  col-form-label" for="address1">Address Line 1</label>
                <div class="col-sm-9 ">
                    <input v-model="collectionAddress['address1']" class="form-control" id="address1" placeholder="Address Line 1">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3  col-form-label" for="address2">Address Line 2</label>
                <div class="col-sm-9 ">
                        <input v-model="collectionAddress['address2']" class="form-control" id="address2" placeholder="Address Line 2"/>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3  col-form-label" for="address3">Address Line 3</label>
                <div class="col-sm-9 ">
                    <input v-model="collectionAddress['address3']" class="form-control" id="address3" placeholder="Address Line 3" required>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3  col-form-label" for="city">City</label>
                <div class="col-sm-9 ">
                    <input v-model="collectionAddress['city']" class="form-control" id="city" placeholder="City" required>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3  col-form-label" for="postcode">Postcode</label>
                <div class="col-sm-9 ">
                    <input v-model="collectionAddress['postcode']" class="form-control" id="postcode" placeholder="Postcode" required>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3  col-form-label" for="state_name">State Name</label>
                <div class="col-sm-9 ">
                    <input v-model="collectionAddress['state_name']" class="form-control" id="state_name" placeholder="State name" required>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="country_code">Country Code</label>
                <div class="col-sm-9">
                    <select v-model="collectionAddress['country_code']" class="form-control" id="country_code">
                        <option value="" selected disabled>Select an option</option>
                        <option value="IE">IE</option>
                        <option value="IRL">IRL</option>
                        <option value="UK">UK</option>
                        <option value="GB">GB</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import { ValidationObserver, ValidationProvider } from "vee-validate";

import Loading from "../../mixins/loading-overlay";
import api from "../../mixins/api";

export default {
    components: {
        ValidationObserver,
        ValidationProvider,
    },

    mixins: [api, Loading],

    data: () => ({
        connection: {},
        collectionAddress: {},
    }),

    methods: {
        submit() {
            this.showLoading();

            this.connection['collection_address'] = this.collectionAddress;

            this.apiPostDpdUkConnection(this.connection)
                .then(() => {
                    this.$emit("saved", this.connection);
                })
                .catch((error) => {
                    this.displayApiCallError(error);
                })
                .finally(() =>{
                        this.hideLoading();
                    }
                );
        },
    },
};
</script>
