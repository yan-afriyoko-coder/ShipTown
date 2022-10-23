<template>
    <div class="row mb-3">
        <div class="col ml-0 pl-0">
            <div class="card ml-0 pl-0">
                <div class="card-body pt-2 pl-2">
                    <div class="row mt-0">
                        <div class="col-lg-4">
                            <div class="text-primary h5">{{ record['product_name'] }}</div>
                            <div>
                                sku: <b>
                                <font-awesome-icon icon="copy" class="fa-xs btn-link" role="button" @click="copyToClipBoard(record['product_sku'])"></font-awesome-icon>
                                <a target="_blank"  :href="'/products?hide_nav_bar=true&search=' + record['product_sku']">{{ record['product_sku'] }}</a>
                            </b>
                            </div>
                            <div>
                                <template v-for="tag in record['tags']">
                                    <a class="badge text-uppercase" :key="tag.id" @click.prevent="setUrlParameterAngGo('filter[has_tags]', tag['name']['en'])"> {{ tag['name']['en'] }} </a>
                                </template>
                            </div>
                            <div class="small">
                                location: {{ record['warehouse_code'] }}
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="row pt-1">
                                <div class="col-12 text-right" @click="expanded = !expanded">
                                    <div class="row">
                                        <div class="text-nowrap text-right col-lg-6">
                                        </div>
                                        <div class="text-nowrap text-right col-lg-6">
                                            <number-card class="mr-lg-4" label="restock level" :number="record['restock_level']" v-bind:class="{'bg-warning' : record['restock_level'] <= 0 }"></number-card>
                                            <number-card label="in stock" :number="record['quantity_available']" v-bind:class="{'bg-warning' : record['quantity_available'] < 0 }"></number-card>
                                            <number-card label="incoming" :number="record['quantity_incoming']"></number-card>
                                            <number-card label="required" :number="record['quantity_required']"></number-card>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div @click="expanded = !expanded" class="text-center d-inline-block align-text-top col-sm-12 border-0 p-sm-0 pt-1">
                        <font-awesome-icon v-if="expanded" icon="chevron-up" class="fa fa-xs text-secondary"></font-awesome-icon>
                        <font-awesome-icon v-if="!expanded" icon="chevron-down" class="fa fa-xs"></font-awesome-icon>
                    </div>

                    <div class="row text-center align-content-center" v-if="expanded">
                        <div class="d-sm-none d-md-block col-6">

                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="text-nowrap text-right col-lg-12">
                                <number-card class="" label="reorder point" :number="record['reorder_point']" v-bind:class="{'bg-warning' : record['reorder_point'] <= 0 }"></number-card>
                                <text-card class="mr-lg-4"  label="last counted" :text="formatDateTime(record['last_counted_at'],'D MMM')" ></text-card>
                                <text-card label="" text="" ></text-card>
                                <number-card label="warehouse" :number="record['warehouse_quantity']" class="" ></number-card>
                            </div>

                            <hr>

                            <div class="col-12 text-nowrap">
                                <stocktake-input :inputId="'stocktake-input-inventory-id-' + record['inventory_id']"></stocktake-input>
                            </div>

                            <div class="col-12 mt-3">
                                <label class="small">restock level</label>
                            </div>
                            <div class="col-12 text-nowrap">
                                <div class="input-group mb-3">
                                    <button tabindex="-1" @click="minusRestockLevel" class="btn btn-danger mr-3" type="button" id="button-addon3" style="min-width: 45px">-</button>
                                    <input tabindex="0"
                                           @keyup="onUpdateRestockLevelEvent"
                                           v-model="newRestockLevelValue"
                                           @focus="simulateSelectAll"
                                           type="number"
                                           inputmode="numeric"
                                           class="form-control text-center"
                                           style="font-size: 24px"
                                           >
                                    <button tabindex="-1" @click="plusRestockLevel" class="btn btn-success ml-3" type="button" id="button-addon4" style="min-width: 45px">+</button>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="small">reorder point</label>
                            </div>
                            <div class="col-12 text-nowrap">
                                <div class="input-group mb-3">
                                    <button tabindex="-1" @click="minusReorderPoint" class="btn btn-danger mr-3" type="button" id="button-addon5" style="min-width: 45px">-</button>
                                    <input tabindex="0"
                                           @keyup="onUpdateReorderPointEvent"
                                           v-model="newReorderPointValue"
                                           @focus="simulateSelectAll"
                                           type="number"
                                           inputmode="numeric"
                                           class="form-control text-center"
                                           style="font-size: 24px"
                                           >
                                    <button tabindex="-1" @click="plusReorderPoint" class="btn btn-success ml-3" type="button" id="button-addon6" style="min-width: 45px">+</button>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import loadingOverlay from '../../mixins/loading-overlay';
import helpers from "../../mixins/helpers";
import api from "../../mixins/api";
import url from "../../mixins/url";

export default {
        name: "RestockingRecord",
        mixins: [loadingOverlay, url, api, helpers],

        props: {
            record: null,
        },

        data: function() {
            return {
                expanded: false,
            };
        },

        computed: {
            newRestockLevelValue: {
                get: function() {
                    return Number(this.record['restock_level']);
                },
                set: function(newValue) {
                    this.record['restock_level'] = Number(newValue);
                }
            },

            newReorderPointValue: {
                get: function() {
                    return Number(this.record['reorder_point']);
                },
                set: function(newValue) {
                    this.record['reorder_point'] = Number(newValue);
                }
            },
        },

        methods: {
            minusRestockLevel() {
                if (Number(this.record['restock_level']) <= 0) {
                    this.updateRestockLevel(Number(this.record['quantity_in_stock']) - 1);

                    if (Number(this.record['reorder_point']) === 0) {
                        this.updateReorderPoint(Math.floor(Number(this.record['restock_level']) / 2));
                    }

                    return;
                }

                this.updateRestockLevel(Number(this.record['restock_level']) - 1);
            },

            plusRestockLevel() {
                if (Number(this.record['restock_level']) === 0) {
                    this.updateRestockLevel(Math.max(0, Number(this.record['quantity_in_stock'])) + 1);

                    if (Number(this.record['reorder_point']) === 0) {
                        this.updateReorderPoint(Math.ceil(Number(this.record['restock_level']) / 2));
                    }

                    return;
                }

                this.updateRestockLevel(Number(this.record['restock_level']) + 1);
            },

            minusReorderPoint() {
                if (Number(this.record['reorder_point']) === 0) {
                    this.updateReorderPoint(Math.ceil(Number(this.record['quantity_in_stock']) / 2) - 1);
                    return;
                }

                this.updateReorderPoint(Number(this.record['reorder_point']) - 1);
            },

            plusReorderPoint() {
                if (Number(this.record['reorder_point']) === 0) {
                    let value = Math.min(this.record['quantity_in_stock'], this.record['restock_level'], );
                    this.updateReorderPoint(Math.ceil(Number(value) / 2) + 1);
                    return;
                }

                this.updateReorderPoint(Number(this.record['reorder_point']) + 1);
            },

            onUpdateRestockLevelEvent(keyboard_event) {
                this.updateRestockLevel(keyboard_event.target.value);
            },

            onUpdateReorderPointEvent(keyboard_event) {
                this.updateReorderPoint(keyboard_event.target.value);
            },

            postInventoryUpdate() {
                const originalQuantityRequired = Number(this.record['quantity_required']);
                const originalRestockLevel = Number(this.record['restock_level']);
                const originalReorderPoint = Number(this.record['reorder_point']);

                this.apiPostInventory({
                        'id': this.record['inventory_id'],
                        'restock_level': this.record['restock_level'],
                        'reorder_point': this.record['reorder_point'],
                    })
                    .then(response => {
                        this.record['quantity_required'] = response.data.data[0]['quantity_required'];
                    })
                    .catch(error => {
                        this.record['quantity_required'] = originalQuantityRequired;
                        this.record['restock_level'] = originalRestockLevel;
                        this.record['reorder_point'] = originalReorderPoint;
                        this.notifyError(error);
                    });
            },
            updateRestockLevel(value) {
                this.record['restock_level'] = Math.max(0, Number(value));
                this.record['reorder_point'] = Math.min(Number(this.record['restock_level']), Number(this.record['reorder_point']));

                this.postInventoryUpdate();
            },

            updateReorderPoint(value) {
                this.record['reorder_point'] = Math.max(0, Number(value));
                this.record['restock_level'] = Math.max(Number(this.record['restock_level']), Number(this.record['reorder_point']));

                this.postInventoryUpdate();
            },
        },
    }
</script>

<style lang="scss" scoped>
    //.row {
    //    display: flex;
    //    justify-content: center;
    //    align-items: center;
    //}
</style>
