<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        API2Cart Connections
                    </span>
                    <a tabindex="-1" class="action-link" v-b-modal.api2cart-connection-modal>
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
                            <th>Store Type</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(configuration, i) in configurations" :key="i">
                            <td>{{ configuration.location_id }}</td>
                            <td>{{ configuration.url }}</td>
                            <td>{{ configuration.type | capitalize }}</td>
                            <td>
                                <a @click="handleDelete(configuration.id, i)" class="action-link text-danger">Delete</a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p v-else class="mb-0">
                    You have not created any API2Cart connections.
                </p>
            </div>
        </div>

        <!-- The modal -->
        <b-modal ref="formModal" id="api2cart-connection-modal" title="Configure Connection" @ok="handleModalOk">
            <api-configuration-form ref="form" @saved="handleSaved" />
        </b-modal>
    </div>
</template>

<script>
    import { BModal, VBModal, BButton } from 'bootstrap-vue';

    import Api2CartConfigurationForm from '../SharedComponents/Api2CartConfigurationForm';

    export default {
        components: {
            'b-modal': BModal,
            'b-button': BButton,
            'api-configuration-form': Api2CartConfigurationForm,
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

            getConfiguration() {
                axios.get('/api/api2cart_configuration')
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
                axios.delete(`/api/api2cart_configuration/${id}`).then(() => {
                    Vue.delete(this.configurations, index);
                });
            }
        },

        filters: {
            capitalize: function (value) {
                if (!value) return ''
                value = value.toString()
                return value.charAt(0).toUpperCase() + value.slice(1)
            }
        }
    }

</script>

<style lang="scss" scoped>
    .action-link {
        cursor: pointer;
    }
</style>
