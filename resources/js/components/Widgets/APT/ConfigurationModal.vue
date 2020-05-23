<template>
    <div class="modal fade widget-configuration-modal" tabindex="-1" role="dialog" aria-labelledby="aptConfigurationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">30 DAY APT</h5>
                    <div class="widget-tools-container">
                        <font-awesome-icon icon="question-circle" :content="helpText" v-tippy></font-awesome-icon>
                    </div>
                </div>
                <div class="modal-body">
                    <form method="POST" action="gago" @submit.prevent="handleSubmit">
                        <div class="form-group form-check">
                            <input name="complete" type="checkbox" class="form-check-input" id="cb-complete" />
                            <label class="form-check-label" for="cb-complete">complete</label>
                        </div>
                        <div class="form-group form-check">
                            <input name="cancelled" type="checkbox" class="form-check-input" id="cb-cancelled" />
                            <label class="form-check-label" for="cb-cancelled">cancelled</label>
                        </div>
                        <div class="form-group form-check">
                            <input name="on_hold" type="checkbox" class="form-check-input" id="cb-on_hold" />
                            <label class="form-check-label" for="cb-on_hold">on_hold</label>
                        </div>
                        <div class="form-group form-check">
                            <input name="processing" type="checkbox" class="form-check-input" id="cb-processing" />
                            <label class="form-check-label" for="cb-processing">processing</label>
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
        statuses: Array,
    },

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
            
        }
    }
}
</script>