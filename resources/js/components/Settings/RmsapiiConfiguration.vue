<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        RMS API Connections
                    </span>
                    <a tabindex="-1" class="action-link" v-b-modal.connection-modal>
                        Create New Connection
                    </a>
                </div>
            </div>

            <div class="card-body">
                <table v-if="configurations.length > 0" class="table table-borderless table-responsive mb-0">
                    <thead>
                        <tr>
                            <th>Location ID</th>
                            <th>URL</th>
                            <th>Username</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(configuration, i) in configurations" :key="i">
                            <td>{{ configuration.location_id }}</td>
                            <td>{{ configuration.url }}</td>
                            <td>{{ configuration.username }}</td>
                            <td>
                                <a @click="handleDelete(configuration.id, i)" class="action-link text-danger">Delete</a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p v-else class="mb-0">
                    You have not created any RMS API connections.
                </p>
            </div>
        </div>

        <!-- The modal -->
        <b-modal ref="formModal" id="connection-modal" title="Configure Connection" @ok="handleModalOk">
            <rms-api-configuration-form ref="form" @saved="handleSaved" />
        </b-modal>
    </div>
</template>

<script>
    import { BModal, VBModal, BButton } from 'bootstrap-vue';

    import RMSApiConfigurationForm from './RmsapiConfigurationForm';
    import api from "../../mixins/api";

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
            configurations: []
        }),

        mounted() {
            this.prepareComponent();
        },

        methods: {
            prepareComponent() {
                this.getConfiguration();
            },

            /**
             * Get all of the personal access tokens for the user.
             */
            getConfiguration() {
                this.apiGetRmsapiConnections()
                    .then(({ data }) => {
                        this.configurations = data.data;
                    });
            },

            handleModalOk(bvModalEvt) {
                bvModalEvt.preventDefault();
                this.$refs.form.submit();
            },

            handleSaved(config) {
                this.configurations.push(config);

                this.$nextTick(() => {
                    this.$refs.formModal.hide();
                });
            },

            handleDelete(id, index) {
                this.apiDeleteRmsapiConnection(id)
                    .then(() => {
                        Vue.delete(this.configurations, index);
                    });
            }
        },
    }

</script>

<style lang="scss" scoped>
    .action-link {
        cursor: pointer;
    }
</style>
