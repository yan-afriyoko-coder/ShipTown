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
<!--                            <div class="form-group form-check">-->
<!--                                <input v-model="filters.single_line_orders_only" type="checkbox" class="form-check-input" />-->
<!--                                <label class="form-check-label" >Show single line orders only</label>-->
<!--                            </div>-->
<!--                            <div class="form-group form-check">-->
<!--                                <input v-model="filters.in_stock_only" type="chec kbox" class="form-check-input" />-->
<!--                                <label class="form-check-label" >In stock only</label>-->
<!--                            </div>-->
                            <div class="form-group form-check">
                                <div>Inventory Location ID</div>
                                <div>
                                    <label>
                                        <input v-model="filters['inventory_source_location_id']" type="number" class="form-check-input" />
                                    </label>
                                </div>
                            </div>
                            <div class="form-group form-check">
                                <div>Order Number: </div>
                                <div>
                                    <label>
                                        <input v-model="filters['order_number']" type="number" class="form-check-input" />
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <slot name="actions" v-bind:filters="filters"></slot>
    <!--                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>-->
                        <button type="button" @click.prevent="handleSubmit" class="btn btn-primary">OK</button>
                    </div>
                </div>
            </div>
        </div>
</template>

<script>
    import url from "../../../mixins/url";

    export default {
        name: 'FiltersModal',

        mixins: [url],

        data: function() {
            return {
                filters: {
                    order_number: this.getUrlParameter('order_number',  null),
                    inventory_source_location_id: this.getUrlParameter('inventory_source_location_id',  100),
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
