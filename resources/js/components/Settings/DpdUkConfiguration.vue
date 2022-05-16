<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        DPD UK Integration Configuration
                    </span>
                </div>
            </div>

            <div class="card-body">

                <p v-if="configurations.length === 0" class="flex-fill text-center">
                    No connections found
<!--                    <br><br>-->
<!--                    <button class="btn btn-sm btn-primary">NEW</button>-->
                </p>

                <table v-if="configurations.length" class="table table-borderless table-responsive mb-0">
                    <thead>
                        <tr>
                            <th>API Username</th>
                            <th>Collection Address</th>
<!--                            <th></th>&lt;!&ndash;Delete&ndash;&gt;-->
                        </tr>
                    </thead>
                    <tbody>
                    <template v-for="configuration in configurations">
                        <tr key="{{ configuration.id }}">
                            <td>{{ configuration.username }}</td>
                            <td>
                                <div>{{ configuration.collection_address.first_name }} {{ configuration.collection_address.last_name }}</div>
                                <div>{{ configuration.collection_address.compnay }}</div>
                                <div>{{ configuration.collection_address.address1 }}</div>
                                <div>{{ configuration.collection_address.address2 }}</div>
                                <div>{{ configuration.collection_address.city }}</div>
                                <div>{{ configuration.collection_address.country_code }}</div>
                                <div>{{ configuration.collection_address.phone }}</div>
                                <div></div>
                                <div>{{ configuration.collection_telephone }}</div>
                                <div>{{ configuration.collection_email }}</div>
                            </td>
<!--                            <td>-->
<!--                                <a @click="handleDelete(configuration.id)" class="action-link text-danger">Delete</a>-->
<!--                            </td>-->
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- The modal -->
        <b-modal ref="createFormModal" id="dpd-create-form-modal" title="Create DPD Configuration" @ok="handleModalOk">
            <dpd-configuration-form ref="createForm" @saved="handleSaved" />
            <template #modal-footer="{ ok, cancel }">
                <b-button @click="cancel()">
                    Cancel
                </b-button>
                <b-button variant="primary" @click="ok()">
                    Save
                </b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
    import { BModal, VBModal, BButton} from 'bootstrap-vue';

    import DpdConfigurationForm from '../SharedComponents/DpdConfigurationForm.vue';
    import api from "../../mixins/api";
    import helpers from "../../mixins/helpers";

    export default {
        components: {
            'b-modal': BModal,
            'b-button': BButton,
            'dpd-configuration-form': DpdConfigurationForm,
        },

        mixins: [api, helpers],

        directives: {
            'b-modal': VBModal
        },

        data: () => ({
            configurations: [],
            has_configuration : false,
        }),

        mounted() {
            this.getConfiguration();
        },

        methods: {
            getConfiguration() {
                this.apiGetDpdUkConnections()
                    .then(({ data }) => {
                        this.configurations = data.data;
                    })
                    .catch((error) => {
                        this.notifyError('Api call failed - Error ' + error.response.status +': '+ error.response.statusText);
                    });
            },

            handleModalOk(bvModalEvt) {
                bvModalEvt.preventDefault();

                if(this.configuration) {
                    this.$refs.updateForm.submit();
                } else {
                    this.$refs.createForm.submit();
                }

                this.getConfiguration();
            },

            handleSaved(data) {
                this.configuration = data;
                this.$nextTick(() => {
                    if (this.configuration) {
                        this.$refs.updateFormModal.hide();
                    } else {
                        this.$refs.createFormModal.hide();
                    }
                });
            },

            handleDelete(id) {
                this.apiDeleteDpdConfiguration(id)
                    .then(() => {
                        this.configurations = [];
                    });
            },
        }
    }

</script>

<style lang="scss" scoped>
    .action-link {
        cursor: pointer;
    }
</style>
