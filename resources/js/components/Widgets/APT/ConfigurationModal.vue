<template>
    <div class="modal fade widget-configuration-modal" tabindex="-1" role="dialog" aria-labelledby="aptConfigurationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div ref="loadingContainer" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">30 DAY APT</h5>
                    <div class="widget-tools-container">
                        <font-awesome-icon icon="question-circle" :content="helpText" v-tippy></font-awesome-icon>
                    </div>
                </div>
                <div class="modal-body" style="margin: 0 auto 0;">
                    <form method="POST" action="gago" @submit.prevent="handleSubmit">
                        <div class="form-group form-check">
                            <input v-model="config.complete" name="complete" type="checkbox" class="form-check-input" id="cb-complete" />
                            <label class="form-check-label" for="cb-complete">complete</label>
                        </div>
                        <div class="form-group form-check">
                            <input v-model="config.complete_manually_processed" name="complete_manually_processed" type="checkbox" class="form-check-input" id="cb-complete_manually_processed" />
                            <label class="form-check-label" for="cb-complete_manually_processed">complete_manually_processed</label>
                        </div>
                        <div class="form-group form-check">
                            <input v-model="config.completed_imported_to_rms" name="completed_imported_to_rms" type="checkbox" class="form-check-input" id="cb-completed_imported_to_rms" />
                            <label class="form-check-label" for="cb-completed_imported_to_rms">completed_imported_to_rms</label>
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
import loadingOverlay from '../../../mixins/loading-overlay';

export default {
    mixins: [loadingOverlay],

    created() {
        if (this.widgetId) {
            this.id = this.widgetId;
        }

        this.config = Object.assign({}, this.config, this.widgetConfig);
    },

    props: {
        statuses: Array,
        widgetConfig: [Array, Object],
        widgetId: [Number, String],
        name: String,
    },

    data: () => ({
        id: null,
        config: {
            complete: false,
            complete_manually_processed: false,
            completed_imported_to_rms: false,
        }
    }),

    computed: {
        helpText() {
            let text = 'APT - Average Processing Time<br>' +
                        '<br>' +
                        'This is the average time difference between time when order has been placed' +
                        'and time when status was first changed to something different that "processsing" <br>' +
                        '<br>' +
                        'Only orders with one of the following statuses are taken into calculations:<br><ul>';

            this.statuses.forEach(status => {
                text += `<li>${status}</li>`;
            });

            text += '</ul>';
            return text;
        }
    },

    methods: {
        handleSubmit() {
            let url = '/api/widgets';
            let method = 'post';
            const data = {
                name: this.name,
                config: this.config
            };

            if (this.id) {
                url = `/api/widgets/${this.id}`;
                method = 'put';
            }

            this.showLoading();

            axios({
                method,
                data,
                url
            }).then(({ data }) => {
                const widget = data.data;
                this.id = widget.id;
                this.config = widget.config;

                this.$snotify.success('APT Widget configuration saved.');
                $(this.$el).modal('hide');
                window.location.reload();
            }).then(this.hideLoading);
        }
    }
}
</script>