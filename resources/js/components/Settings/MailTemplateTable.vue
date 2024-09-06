<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Mail Templates
                    </span>
                    <button @click="showNewMailTemplateModal" type="button" class="btn btn-primary ml-2">
                        <font-awesome-icon icon="plus" class="fa-lg"></font-awesome-icon>
                    </button>
                </div>
            </div>

            <div class="card-body">
                <table v-if="mailTemplates.length > 0" class="table table-borderless table-responsive mb-0">
                    <tbody>
                        <tr v-for="(mailTemplate, i) in mailTemplates" :key="i">
                            <td>
                                <a :href='"mail-templates/" + mailTemplate.id + "/preview"' target="_blank">
                                    {{ mailTemplate.name }}
                                </a>
                            </td>
                            <td>
                                <a @click.prevent="showEditForm(mailTemplate)">
                                    <font-awesome-icon icon="edit"></font-awesome-icon>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p v-else class="mb-0">
                    No mail template found.
                </p>
            </div>
        </div>
        <edit-modal :mailTemplate="selectedMailTemplate" id="editForm" @onUpdated="updateMailTemplate"></edit-modal>
    </div>
</template>

<script>

import EditModal from './MailTemplate/EditModal';
import api from "../../mixins/api.vue";
import Modals from "../../plugins/Modals";

export default {
    mixins: [api],
    components: {
        'edit-modal': EditModal,
    },

    mounted() {
        this.apiGetMailTemplate()
            .then(({ data }) => {
                this.mailTemplates = data.data;
            });
        Modals.EventBus.$on('mailTemplateCreated', this.handleMailTemplateCreated);
    },

    data: () => ({
        mailTemplates: [],
        selectedMailTemplate: {}
    }),

    beforeDestroy() {
        Modals.EventBus.$off('mailTemplateCreated', this.handleMailTemplateCreated);
    },

    methods: {
        showNewMailTemplateModal() {
            console.log('clicked')
            this.$modal.showAddNewMailTemplateModal();
        },
        showEditForm(mailTemplate) {
            this.selectedMailTemplate = mailTemplate;
            $('#editForm').modal('show');
        },
        handleMailTemplateCreated(newData) {
            this.mailTemplates = newData;
        },
        updateMailTemplate(newValue) {
            const indexMailTemplate = this.mailTemplates.findIndex(mailTemplate => mailTemplate.id == newValue.id);
            this.$set(this.mailTemplates, indexMailTemplate, newValue);
        },
    },
}
</script>
