<template>
    <ValidationObserver ref="form">
        <form class="form" @submit.prevent="submit" ref="loadingContainer">
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
        </form>
    </ValidationObserver>
</template>

<script>
import { ValidationObserver, ValidationProvider } from 'vee-validate';

import Loading from '../../mixins/loading-overlay';

export default {
    components: {
        ValidationObserver, ValidationProvider
    },

    mixins: [Loading],

    data: () => ({
        email: null
    }),

    methods: {
        submit() {
            this.showLoading();
            axios.post('/api/admin/user/invites', {
                email: this.email,
            }).then(({ data }) => {
                this.$emit('saved');
                this.$snotify.success('User invite sent.');
                this.reset();
            }).catch((error) => {
                if (error.response) {
                    if (error.response.status === 422) {
                        this.$refs.form.setErrors(error.response.data.errors);
                    }
                }
            }).then(this.hideLoading);
        },

        reset() {
            this.email = null;
        }
    }
}
</script>
