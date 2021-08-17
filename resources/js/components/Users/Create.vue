<template>
    <ValidationObserver ref="form">
        <form class="form" @submit.prevent="submit" ref="loadingContainer">
            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="name">Name</label>
                <div class="col-sm-9">
                    <ValidationProvider vid="name" name="name" v-slot="{ errors }">
                        <input v-model="name" :class="{
                            'form-control': true,
                            'is-invalid': errors.length > 0,
                        }" id="name" placeholder="John Doe" required>
                        <div class="invalid-feedback">
                            {{ errors[0] }}
                        </div>
                    </ValidationProvider>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="email">Email</label>
                <div class="col-sm-9">
                    <ValidationProvider vid="email" name="email" v-slot="{ errors }">
                        <input type="email" v-model="email" :class="{
                            'form-control': true,
                            'is-invalid': errors.length > 0,
                        }" id="email" placeholder="foo@bar.com" required>
                        <div class="invalid-feedback">
                            {{ errors[0] }}
                        </div>
                    </ValidationProvider>
                </div>
            </div>
            <div class="form-group row">
                <label for="role_id" class="col-sm-3 col-form-label">Role</label>
                <div class="col-sm-9">
                    <ValidationProvider vid="role_id" name="role_id" v-slot="{ errors }">
                        <select v-model="roleId" :class="{
                                'form-control': true,
                                'is-invalid': errors.length > 0,
                            }"
                            id="role_id" required>
                            <option value=""></option>
                            <option v-for="(role, i) in roles" :key="i" :value="role.id">
                                {{ role.name }}
                            </option>
                        </select>
                        <div class="invalid-feedback">
                            {{ errors[0] }}
                        </div>
                    </ValidationProvider>
                </div>
            </div>
        </form>
    </ValidationObserver>
</template>

<script>
import { ValidationObserver, ValidationProvider } from 'vee-validate';

import Loading from '../../mixins/loading-overlay';
import api from "../../mixins/api";

export default {
    components: {
        ValidationObserver, ValidationProvider
    },

    props: {
        roles: Array
    },

    mixins: [api, Loading],

    data: () => ({
        name: null,
        email: null,
        roleId: null
    }),

    methods: {

        submit() {
            this.showLoading();
            let data = {
                name: this.name,
                email: this.email,
                role_id: this.roleId,
            };
            this.apiPostUserStore(data)
                .then(({ data }) => {
                    this.$snotify.success('User created.');
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
        }
    }
}
</script>
