<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        DPD Ireland Configuration
                    </span>
                    <a tabindex="-1" class="action-link" v-if="!has_configuration" v-b-modal.dpd-create-form-modal>
                        Create New Configuration
                    </a>
                </div>
            </div>

            <div class="card-body">
                <table v-if="has_configuration" class="table table-borderless table-responsive mb-0">
                    <thead>
                        <tr>
                            <th>API Username</th>
                            <th>API Live</th>
                            <th>Contact</th>
                            <th>Telephone</th>
                            <th>Email</th>
                            <th>Business Name</th>
                            <th>Address</th>
                            <th></th><!--Edit Configuration-->
                            <th></th><!--Delete-->
                        </tr>
                    </thead>
                    <tbody>
                        <tr key="1">
                            <td>{{ configuration.api_username }}</td>
                            <td>{{ configuration.api_live ? 'true' : 'false'}}</td>
                            <td>{{ configuration.collection_contact }}</td>
                            <td>{{ configuration.collection_telephone }}</td>
                            <td>{{ configuration.collection_email }}</td>
                            <td>{{ configuration.collection_business_name }}</td>
                            <td>{{ configurationAddress }}</td>
                            <td>
                                <a class="action-link" v-b-modal.dpd-update-form-modal>Edit</a>
                            </td>
                            <td>
                                <a @click="handleDelete(configuration.id)" class="action-link text-danger">Delete</a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p v-else class="mb-0">
                    You have not created any DPD Ireland Configuration.
                </p>
            </div>
        </div>

        <!-- The modal -->
        <b-modal ref="createFormModal" id="dpd-create-form-modal" title="Create DPD Configuration" @ok="handleModalOk">
            <dpd-configuration-form ref="createForm" @saved="handleSaved" />
        </b-modal>

        <!-- The modal -->
        <b-modal ref="updateFormModal" id="dpd-update-form-modal" title="Update DPD Configuration" @ok="handleModalOk">
            <dpd-configuration-form ref="updateForm" @saved="handleSaved" :configuration="configuration"/>
        </b-modal>
    </div>
</template>

<script>
    import { BModal, VBModal, BButton} from 'bootstrap-vue';

    import DpdConfigurationForm from '../SharedComponents/DpdConfigurationForm.vue';
    import api from "../../mixins/api";

    export default {
        components: {
            'b-modal': BModal,
            'b-button': BButton,
            'dpd-configuration-form': DpdConfigurationForm,
        },

        mixins: [api],

        directives: {
            'b-modal': VBModal
        },

        data: () => ({
            configuration: {},
            has_configuration : true,
        }),

        mounted() {
            this.prepareComponent();
        },

        computed: {
            configurationAddress: function () {
                const address = [
                    this.configuration.collection_address_line_1,
                    this.configuration.collection_address_line_2,
                    this.configuration.collection_address_line_3,
                    this.configuration.collection_address_line_4,
                    this.configuration.collection_country_code
                ].filter(each => {
                    return !!each;
                });

                return address.join(', ');
            }
        },

        methods: {

            prepareComponent() {
                this.getConfiguration();
            },

            getConfiguration() {
                this.apiGetDpdConfiguration()
                    .then(({ data }) => {
                        this.configuration = data.data;
                    }).catch((error) => {
                        this.has_configuration = false;
                    });
            },

            handleModalOk(bvModalEvt) {
                bvModalEvt.preventDefault();

                if(this.has_configuration) {
                    this.$refs.updateForm.submit();
                } else {
                    this.$refs.createForm.submit();
                }
            },

            handleSaved(data) {
                this.configuration = data;

                this.$nextTick(() => {
                    if (this.has_configuration) {
                        this.$refs.updateFormModal.hide();
                    } else {
                        this.$refs.createFormModal.hide();
                    }
                });

                this.getConfiguration();
            },

            handleDelete(id) {
                this.apiDeleteDpdConfiguration(id)
                    .then(() => {
                        this.configuration = {};
                        this.has_configuration = false;
                    });
            }
        }
    }

</script>

<style lang="scss" scoped>
    .action-link {
        cursor: pointer;
    }
</style>
