<template>
    <div class="modal fade widget-configuration-modal" tabindex="-1" role="dialog" aria-labelledby="picklistConfigurationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div ref="loadingContainer" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Picklist</h5>
                    <div class="widget-tools-container">
                        <font-awesome-icon icon="question-circle" :content="helpText" v-tippy></font-awesome-icon>
                    </div>
                </div>
                <div class="modal-body" style="margin: 0 auto 0;">
                    <form method="POST" action="gago" @submit.prevent="handleSubmit">
                        <div class="form-group form-check">
                            <input v-model="config.single_line_orders_only" type="checkbox" class="form-check-input" />
                            <label class="form-check-label" >Show single line orders only</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" @click.prevent="handleSubmit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {

    props: {
        config: Object,
    },

    computed: {
        helpText() {
            let text = 'APT - Average Processing Time<br>' +
                        '<br>' +
                        'This is the average time difference between time when order has been placed' +
                        'and time when status was first changed to something different that "processsing" <br>' +
                        '<br>' +
                        'Only orders with one of the following statuses are taken into calculations:<br><ul>';

            text += '</ul>';
            return text;
        }
    },

    methods: {
        handleSubmit() {
            this.$emit('btnSaveClicked', this.config);

            $(this.$el).modal('hide');
        }
    }
}
</script>
