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
                        }" id="name" required>
                        <div class="invalid-feedback">
                            {{ errors[0] }}
                        </div>
                    </ValidationProvider>
                </div>
            </div>
            <div v-if="!isCurrentUser" class="form-group row">
                <label for="role_id" class="col-sm-3 col-form-label">Role</label>
                <div class="col-sm-9">
                    <select class="form-control" v-model="roleId" name="role_id" id="role_id">
                        <option value=""></option>
                        <option v-for="(role, i) in roles" :key="i" :value="role.id">
                            {{ role.name }}
                        </option>
                    </select>
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
    mixins: [api, Loading],

    components: {
        ValidationObserver, ValidationProvider
    },

    mounted() {
        this.showLoading();
        this.apiGetUsers({
                'filter[user_id]': this.id
            })
            .then(({ data }) => {
                console.log(data);
                const user = data.data[0];
                this.name = user.name;
                this.roleId = user.role_id;
            })
            .finally(this.hideLoading);
    },

    props: {
        id: Number,
        roles: Array
    },

    data: () => ({
        name: null,
        roleId: null,
    }),

    methods: {

        submit() {
            this.showLoading();
            this.apiPostUserUpdate(this.id, {
                    name: this.name,
                    role_id: this.roleId,
                })
                .then(({ data }) => {
                    this.$emit('saved');
                    this.$snotify.success('User updated.');
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
    },

    computed: {
        isCurrentUser() {
            let token = document.head.querySelector('meta[name="user-id"]');

            return parseInt(token.content) === this.id;
        }
    }
}
</script>
