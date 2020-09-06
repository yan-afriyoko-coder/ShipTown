<template>
    <div class="modal fade set-shipping-number-modal"  id="shippingNumberModal" tabindex="-1" role="dialog" aria-labelledby="setShippingNumberModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div ref="setShippingNumberModal" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Shipping Number</h5>
                    <div class="widget-tools-container">
                        <font-awesome-icon icon="question-circle" :content="helpText" v-tippy></font-awesome-icon>
                    </div>
                </div>
                <div class="modal-body" style="margin: 0 auto 0;">
                    <form method="POST" @submit.prevent="setShippingNumber">
                        <!--                            <div class="form-group form-check">-->
                        <!--                                <input v-model="filters.single_line_orders_only" type="checkbox" class="form-check-input" />-->
                        <!--                                <label class="form-check-label" >Show single line orders only</label>-->
                        <!--                            </div>-->
                        <!--                            <div class="form-group form-check">-->
                        <!--                                <input v-model="filters.in_stock_only" type="chec kbox" class="form-check-input" />-->
                        <!--                                <label class="form-check-label" >In stock only</label>-->
                        <!--                            </div>-->
                        <div class="form-group form-check">
                            <label>
                                <input ref="shipping_number" class="form-control" placeholder="Scan shipping number"
                                       v-observe-visibility="simulateSelectAll"
                                       v-model="shipping_number"
                                       @focus="simulateSelectAll"
                                       @keyup.enter="setShippingNumber"/>

                                <!--<input v-model="shipping_number" type="number" class="form-check-input" />-->
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer text-center">
<!--                    <slot name="actions" v-bind:filters="filters"></slot>-->
<!--                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>-->
                    <button type="button" @click.prevent="setShippingNumber" class="btn btn-primary">Pack & Ship</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "SetShippingNumberModal",

    data: function() {
        return {
            shipping_number: ''
        }
    },

    computed: {
        helpText() {
            return 'Single line orders are orders with only single product ordered.';
        }
    },

    methods: {
        showShippingNumberModal() {
            $(shippingNumberModal).modal('show');
        },

        focusOnInput() {
            this.$refs.shipping_number.focus();
            this.simulateSelectAll();
        },

        setShippingNumber() {
            this.hide();
            this.$emit('shippingNumberUpdated', this.shipping_number);
        },

        simulateSelectAll() {
            setTimeout(() => { document.execCommand('selectall', null, false); });
        },

        hide() {
            $(this.$el).modal('hide');
        }
    }
}
</script>

<style scoped>

</style>
