<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Slack Incoming Webhook
                    </span>
                </div>
            </div>

            <div class="card-body">
                <table v-if="incomingWebhooks.length > 0" class="table table-borderless table-responsive mb-0">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(incomingWebhook, i) in incomingWebhooks" :key="i">
                            <td>
                                <div v-if="incomingWebhook.status === 200" class="badge badge-success text-uppercase"> Status OK </div>
                                <div v-if="incomingWebhook.status !== 200" class="badge badge-danger text-uppercase"> Status {{incomingWebhook.status}} </div>
                            </td>
                            <td>
                                {{ incomingWebhook.url }}<br>
                                <div class="text-small"><span class="font-weight-bold">username: </span> {{ incomingWebhook.username }}</div>
                                <div class="text-small"><span class="font-weight-bold">warehouse: </span> {{ incomingWebhook.location_id }}</div>
                                <div class="text-small"><span class="font-weight-bold">Password Reset Page: </span> <a :href="incomingWebhook.url + '/password/reset'" target="_blank" class="action-link text-primary">link</a></div>
                                <div class="text-small"><span class="font-weight-bold">Status Page: </span> <a :href="incomingWebhook.url + '/status'" target="_blank" class="action-link text-primary">link</a></div>
                                <div class="text-small"><span class="font-weight-bold">Webhooks: </span> {{ incomingWebhook.url + '/api/products-management/webhooks' }}</div>
                            </td>
                            <td>
                                <a @click="confirmDelete(incomingWebhook.id, i)" class="action-link text-danger">DELETE</a><br>
                                <a @click="changePassword(incomingWebhook.id, i)" class="action-link text-danger">UPDATE API PASSWORD</a><br>
                                <br>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p v-else class="mb-0 ">
                    <a tabindex="-1" >
                        <input placeholder="Webhook URL"
                               class="form-control"
                               v-model="incoming_webhook_url"
                               autocomplete="off"
                               dusk="incoming_webhook_url_field"
                               @keyup.enter="saveIncomingWebhook"
                        />
                        <b-button variant="primary" class="mt-3 fa-pull-right" @click="saveIncomingWebhook">Save</b-button>
                    </a>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
    import { BModal, VBModal, BButton } from 'bootstrap-vue';

    import RMSApiConfigurationForm from '../../../Settings/RmsapiConfigurationForm';
    import api from "../../../../mixins/api";

    export default {
        mixins: [api],

        components: {
            'b-modal': BModal,
            'b-button': BButton,
            'rms-api-configuration-form': RMSApiConfigurationForm,
        },

        directives: {
            'b-modal': VBModal
        },

        data: () => ({
            incomingWebhooks: [],
            incoming_webhook_url: '',
        }),

        mounted() {
            this.prepareComponent();
        },

        methods: {
            saveIncomingWebhook() {
                this.apiPostModulesSlackIncomingWebhook({
                        webhook_url: this.incoming_webhook_url
                    })
                    .then(({ data }) => {
                        this.$snotify.success('Incoming webhook saved successfully');
                        this.incomingWebhooks.push(data.data);
                        this.incoming_webhook_url = '';
                    })
                    .catch(({ response }) => {
                        this.$snotify.error(response.data.message);
                    })
                    .finally(() => {
                        this.$snotify.remove(toast.id);
                    });

            },

            prepareComponent() {
                this.getConfiguration();
            },

            /**
             * Get all of the personal access tokens for the user.
             */
            getConfiguration() {
                this.apiGetRmsapiConnections({})
                    .then(({ data }) => {
                        data.data.forEach((item, index) => {
                            item['status'] = 0;
                            this.incomingWebhooks.push(item);

                        })
                        this.incomingWebhooks.forEach((item, index) => {
                            this.apiGet(item['url'] + '/status')
                                .then((response) => {
                                    this.incomingWebhooks[index]['status'] = response.status;
                                })
                        })
                    });
            },

            handleModalOk(bvModalEvt) {
                bvModalEvt.preventDefault();
                this.$refs.form.submit();
            },

            handleSaved(config) {
                this.incomingWebhooks.push(config);

                this.$nextTick(() => {
                    this.$refs.formModal.hide();
                });
            },

            changePassword(id, index){
                this.$snotify.prompt('Enter new password', 'Change Password', {
                    buttons: [
                        {text: 'Cancel', action: (toast) => { this.$snotify.remove(toast.id) }},
                        {text: 'Ok', action: (toast) => {
                                this.apiPostRmsapiConnections({id: id, password: toast.value})
                                    .then(({ data }) => {
                                        this.$snotify.success('Password changed successfully');
                                        this.$snotify.remove(toast.id);
                                    })
                                    .catch(({ response }) => {
                                        this.$snotify.error(response.data.message);
                                        this.$snotify.remove(toast.id);
                                    })
                                    .finally(() => {
                                        this.$snotify.remove(toast.id);
                                    });
                            }
                        }
                    ]
                });
                this.apiPostRmsapiConnections({
                    id: id,
                    password: 'password'
                })
                    .then(({ data }) => {
                        this.incomingWebhooks[index]['password'] = 'password';
                    });
            },

            confirmDelete(id, index){
                this.$snotify.confirm('After delete data cannot restored', 'Are you sure?', {
                    position: 'centerCenter',
                    buttons: [
                        {
                            text: 'Yes',
                            action: (toast) => {
                                this.handleDelete(id, index)
                                this.$snotify.remove(toast.id);
                            }
                        },
                        {text: 'Cancel'},
                    ]
                });
            },

            handleDelete(id, index) {
                this.apiDeleteRmsapiConnection(id)
                    .then(() => {
                        Vue.delete(this.incomingWebhooks, index);
                    });
            },
        },
    }

</script>

<style lang="scss" scoped>
    .action-link {
        cursor: pointer;
    }
</style>
