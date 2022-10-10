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
                        </div>
                        <div class="col-lg-8">
                            <div class="row pt-1">
                                <div class="col-12 text-right" @click="expanded = !expanded">
                                    <div class="row">
                                        <div class="text-nowrap text-right col-lg-6">
                                            <text-card label="location" :text="record['warehouse_code']" ></text-card>
                                            <text-card class="mr-lg-4" label="last counted" :text="record['last_counted_at']" ></text-card>
                                            <number-card label="reorder point" :number="record['reorder_point']" v-bind:class="{'bg-warning' : record['reorder_point'] <= 0 }"></number-card>
                                            <number-card class="mr-lg-4" label="restock level" :number="record['restock_level']" v-bind:class="{'bg-warning' : record['restock_level'] <= 0 }"></number-card>
                                        </div>
                                        <div class="text-nowrap text-right col-lg-6">
                                            <number-card class="mr-lg-4" label="warehouse" :number="record['warehouse_quantity']"></number-card>
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
                        <div class="col-12">
                            <label class="small">required</label>
                        </div>
                        <div class="col-12 text-nowrap">
                            <div class="input-group mb-3">
                                <button tabindex="-1" @click="minusNewRequired(record)" class="btn btn-danger" type="button" id="button-addon5">-</button>
                                <input tabindex="0" @keyup="onUpdateQuantityRequiredEvent" v-model="record['quantity_required']" @focus="simulateSelectAll" type="text" class="form-control" style="font-size: large" placeholder="" aria-label="Example text with button addon" aria-describedby="button-addon1">
                                <button tabindex="-1" @click="plusNewRequired(record)" class="btn btn-success" type="button" id="button-addon6">+</button>
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
            initial_data: null,
            record: null,
        },

        data: function() {
            return {
                data: [],
                expanded: false,
                per_page: 20,
                reachedEnd: false,
                pagesLoaded: 0,
                selectedRecord: null,
                newReorderPoint: null,
                newRestockLevel: null,
                newQuantityInStock: null,
            };
        },

        computed: {
            newQuantityRequired: {
                get: function () {
                    if (this.newReorderPoint < this.newQuantityInStock) {
                        return 0;
                    }
                    return Math.max(0, this.newRestockLevel - this.newQuantityInStock);
                },
                set: function (newValue) {
                    this.newRestockLevel = Number(this.newQuantityInStock) + Number(newValue);
                    this.newReorderPoint = Number(this.newQuantityInStock);
                }
            },
        },

        methods: {
            focusOnInputAndReload() {
                this.setFocusElementById(100,'barcodeInput', true, true);
                this.loadData();
            },

            onUpdateQuantityRequiredEvent(keyboard_event) {
                this.updateQuantityRequired(keyboard_event.target.value);
            },

            updateQuantityRequired(value) {
                const originalQuantityRequired = this.record['quantity_required'];
                const originalRestockLevel = this.record['restock_level'];
                const originalReorderPoint = this.record['reorder_point'];

                this.record['quantity_required'] = Math.max(0, Number(value));
                this.record['restock_level'] = Number(this.record['quantity_in_stock']) + Number(this.record['quantity_incoming']) + Number(this.record['quantity_required']) ;
                this.record['reorder_point'] = Number(this.record['quantity_in_stock']) + Number(this.record['quantity_incoming']) ;

                this.apiPostInventory({
                        'id': this.record['inventory_id'],
                        'restock_level': this.record['restock_level'],
                        'reorder_point': this.record['reorder_point'],
                    })
                    .catch(error => {
                        this.record['quantity_required'] = originalQuantityRequired;
                        this.record['restock_level'] = originalRestockLevel;
                        this.record['reorder_point'] = originalReorderPoint;
                    });
            },

            plusNewQuantityInStock() {
                this.newQuantityInStock = Number(this.newQuantityInStock) + 1;
            },

            minusNewQuantityInStock() {
                this.newQuantityInStock = Math.max(0, Number(this.newQuantityInStock) - 1);
            },

            plusNewRestockLevel() {
                this.newRestockLevel = Number(this.newRestockLevel) + 1;
            },

            minusNewRestockLevel() {
                this.newRestockLevel = Math.max(0, Number(this.newRestockLevel) - 1);
            },

            plusNewReorderPoint() {
                this.newReorderPoint = Number(this.newReorderPoint) + 1;
            },

            minusNewReorderPoint() {
                this.newReorderPoint = Math.max(0, Number(this.newReorderPoint) - 1);
            },

            plusNewRequired(record) {
                this.updateQuantityRequired(Number(record['quantity_required']) + 1);
            },

            minusNewRequired(record) {
                this.updateQuantityRequired(Number(record['quantity_required']) - 1);
            },


            downloadFileAndHideModal() {
                let routeData = this.$router.resolve({
                    path: this.$router.currentRoute.fullPath,
                    query: {filename: "restocking-"+ this.getUrlParameter('filter[warehouse_code]')+".csv"}
                });
                window.open(routeData.href, '_blank');

                this.$bvModal.hide('configuration-modal');
            },

            showUpdateRestockingInfoModal(restocking_record) {
                this.selectedRecord = restocking_record;
                this.newReorderPoint = restocking_record['reorder_point'];
                this.newRestockLevel = restocking_record['restock_level'];
                this.newQuantityInStock = restocking_record['quantity_in_stock'];
                this.$bvModal.show('update-restocking-info-modal');
            },

            loadMore() {
                if (this.isLoading) {
                    return;
                }

                if (! this.isMoreThanPercentageScrolled(70)) {
                    return;
                }

                if (this.reachedEnd) {
                    return;
                }

                // we double per_page every second page load to avoid hitting the API too hard
                // and we will limit it to 100-ish per_page
                if ((this.per_page < 100) && (this.pagesLoaded % 2 === 0)) {
                    this.pagesLoaded = this.pagesLoaded / 2;
                    this.per_page = this.per_page * 2;
                }

                this.loadData(++this.pagesLoaded);
            },

            loadData(page = 1) {
                this.showLoading();

                const params = this.$router.currentRoute.query;
                params['include'] = 'tags';
                params['per_page'] = this.per_page;
                params['page'] = page;

                this.apiGetRestocking(params)
                    .then((response) => {
                        if (page === 1) {
                            this.data = [];
                        }
                        this.reachedEnd = response.data.data.length < this.per_page;

                        this.data = this.data.concat(response.data.data);
                        this.pagesLoaded = page;
                    })
                    .finally(() => {
                        this.hideLoading();
                    });
            },

            findText() {
                this.data = [];
                this.loadData();
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
