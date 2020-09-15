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
<!--                        <div class="form-group form-check">-->
<!--                            <input v-model="picklistFilters['filter[single_line_orders_only]']" type="checkbox" class="form-check-input" />-->
<!--                            <label class="form-check-label" >Show single line orders only</label>-->
<!--                        </div>-->
                        <div class="form-group form-check">
                            <input v-model="urlFilters['in_stock_only']" type="checkbox" class="form-check-input" />
                            <label class="form-check-label" >In stock only</label>
                        </div>
                        <div class="form-group form-check">
                        <div>Inventory Location ID</div>
                            <input v-model="urlFilters['inventory_source_location_id']" type="number" class="form-check-input" />
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
import url from "../../mixins/url";

export default {

    mixins: [url],

    props: {
        picklistFilters: Object,
    },

    data() {
        return {
            urlFilters: {
                'inventory_source_location_id':
                    this.getUrlFilter('inventory_source_location_id', 100),

                'in_stock_only':
                    this.getUrlFilter('in_stock_only', true)
            }
        }
    },

    watch: {
        picklistFilters: {
            handler() {
                // this.updateUrl(this.picklistFilters)
            },
            deep: true
        }
    },

    computed: {
        helpText() {
            return 'Single line orders are orders with only single product ordered.';
        }
    },

    methods: {
        handleSubmit() {
            for (let urlFilter in this.urlFilters) {
                this.setUrlFilter(urlFilter, this.urlFilters[urlFilter]);
            }
            // this.updateUrl(this.urlFilters);
            this.$emit('btnSaveClicked');

            $(this.$el).modal('hide');
        }
    }
}
</script>
