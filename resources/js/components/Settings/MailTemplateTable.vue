<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Mail Templates
                    </span>
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
        <!-- The modals -->
        <edit-modal :mailTemplate="selectedMailTemplate" id="editForm" @onUpdated="updateMailTemplate"></edit-modal>
    </div>
</template>

<script>

import EditModal from './MailTemplate/EditModal';
import api from "../../mixins/api.vue";

export default {
    mixins: [api],
    components: {
        'edit-modal': EditModal,
    },

    mounted() {
        this.apiGetMailTemplate()
            .then(({ data }) => {
                this.mailTemplates = data.data;
            })
    },

    data: () => ({
        mailTemplates: [],
        selectedMailTemplate: {}
    }),

    methods: {
        showEditForm(mailTemplate) {
            this.selectedMailTemplate = mailTemplate;
            $('#editForm').modal('show');
        },
        updateMailTemplate(newValue) {
            const indexMailTemplate = this.mailTemplates.findIndex(mailTemplate => mailTemplate.id == newValue.id)
            this.$set(this.mailTemplates, indexMailTemplate, newValue)
        },
    },
}
</script>
