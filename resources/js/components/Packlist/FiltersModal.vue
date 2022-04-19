<template>
        <div class="modal fade widget-configuration-modal"  id="filterConfigurationModal" tabindex="-1" role="dialog" aria-labelledby="picklistConfigurationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div ref="loadingContainer2" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Settings: Packlist</h5>
                        <div class="widget-tools-container">
                            <font-awesome-icon icon="question-circle" :content="helpText" v-tippy></font-awesome-icon>
                        </div>
                    </div>
                    <div class="modal-body" style="margin: 0 auto 0;">
                        <form method="POST" @submit.prevent="handleSubmit">
                            <slot name="actions" v-bind:filters="filters"></slot>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click.prevent="handleSubmit" class="btn btn-primary">OK</button>
                    </div>
                </div>
            </div>
        </div>
</template>

<script>
    import url from "../../mixins/url";

    export default {
        name: 'FiltersModal',

        mixins: [url],

        data: function() {
            return {
                filters: {
                    order_number: this.getUrlParameter('order_number',  null),
                }
            }
        },

        computed: {
            helpText() {
                return 'Single line orders are orders with only single product ordered.';
            }
        },

        methods: {
            getValueOrDefault: function (value, defaultValue){
                return (value === undefined) || (value === null) ? defaultValue : value;
            },

            handleSubmit() {
                this.updateUrlParameters(this.filters);
                this.hide();
                this.$emit('btnSaveClicked', this.filters);
            },

            hide() {
                $(this.$el).modal('hide');
            }
        }
    }
</script>
