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
                    <form method="POST" @submit.prevent="handleSubmit">
                        <div class="form-group form-check">
                            <input v-model="filters.single_line_orders_only" type="checkbox" class="form-check-input" />
                            <label class="form-check-label" >Show single line orders only</label>
                        </div>
                        <div class="form-group form-check">
                            <input v-model="filters.in_stock_only" type="checkbox" class="form-check-input" />
                            <label class="form-check-label" >In stock only</label>
                        </div>
                        <div class="form-group form-check">
                            <input v-model="filters.inventory_location_id" type="number" class="form-check-input" />
                            <label class="form-check-label" >Inventory Location ID</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
<!--                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>-->
                    <button type="button" @click.prevent="handleSubmit" class="btn btn-primary">OK</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import VueRouter from "vue-router";

    Vue.use(VueRouter);

    const Router = new VueRouter({
        mode: 'history',
    });

    export default {
        name: 'FiltersModal',

        router: Router,

        data: function() {
            const $urlParameters = this.$router.currentRoute.query;

            return {
                filters: {
                    include: 'packlist',
                    single_line_orders_only: this.getValueOrDefault($urlParameters.single_line_orders, false),
                    currentLocation: this.getValueOrDefault($urlParameters.currentLocation,  ''),
                    inventory_location_id: this.getValueOrDefault($urlParameters.inventory_location_id,  100),
                    in_stock_only: this.getValueOrDefault($urlParameters.in_stock_only,  true),
                }
            }
        },

        computed: {
            helpText() {
                return 'Single line orders are orders with only single product ordered.';
            }
        },

        methods: {
            updateUrl: function() {
                history.pushState({},null,'/packlist?'
                    // +'single_line_orders='+this.picklistFilters.single_line_orders_only
                    // +'&currentLocation='+ this.picklistFilters.currentLocation
                    +'&inventory_location_id='+ this.filters.inventory_location_id
                    // +'&in_stock_only='+ this.picklistFilters.in_stock_only
                );
            },

            getValueOrDefault: function (value, defaultValue){
                return (value === undefined) || (value === null) ? defaultValue : value;
            },

            handleSubmit() {

                this.updateUrl();
                this.$emit('btnSaveClicked', this.filters);

                $(this.$el).modal('hide');
            }
        }
    }
</script>
