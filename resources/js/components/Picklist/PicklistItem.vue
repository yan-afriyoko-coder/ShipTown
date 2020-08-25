<template>
    <div :id="elID" class="swiper-container mb-3">
        <div class="swiper-wrapper">

            <div class="swiper-slide error bg-success"></div>

            <div class="swiper-slide">
                <div class="row ml-1 mr-1">
                    <div class="col p-2 pl-3">
                        <div class="row text-left">
                            <div class="col-md-8">
                                <div class="text-primary h4">{{ picklistItem['name_ordered'] }}</div>
                                <div class="text-secondary h5">sku: <span class="font-weight-bold"> {{ picklistItem['sku_ordered'] }} </span></div>
                                <div class="text-secondary h5">product: <span class="font-weight-bold"> {{ product_sku }} </span></div>
                            </div>
                            <div class="col-md-4">
                                <div class="row pt-1 mt-1 text-center border-top">
                                    <div class="col-6">
                                        <div class=""></div>
                                        <div class="text-secondary h6 text-left">order: <span class="font-weight-bold"> {{ picklistItem['order']['order_number'] }} </span></div>
                                        <div class="text-secondary h6 text-left">lines: <span class="font-weight-bold"> {{ picklistItem['order']['product_line_count'] }} </span></div>
                                        <div class="text-secondary h6 text-left">stock: <span class="font-weight-bold"> {{ Math.ceil(picklistItem['inventory_source_quantity']) }} </span></div>
                                    </div>
                                    <div class="col-2">
                                        <div>To Pick</div>
                                        <div class="h3">{{ quantity_requested_integer }}</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="">Shelf</div>
                                        <div class="h3">{{ picklistItem['inventory_source_shelf_location'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="swiper-slide bg-warning"></div>

        </div>
    </div>
</template>

<script>
    import 'swiper/css/swiper.min.css';
    import { Swiper } from 'swiper/js/swiper.esm.js';

    export default {
        created() {
            this.product_sku = this.picklistItem['product'] ? this.picklistItem['product']['sku'] : '';
            this.quantity_requested_integer = Math.ceil(this.picklistItem['quantity_requested']);
        },

        mounted() {
            const self = this;
            // Initialize Swiper
            const swiper = new Swiper('#' + this.elID, {
                initialSlide: 1,
                resistanceRatio: 0,
                speed: 150
            });

            let pickedItem = this.picklistItem;

            // Event will be fired after transition
            swiper.on('transitionEnd', function () {
                if (this.activeIndex === 0) {
                    self.$emit('swipeRight', pickedItem);
                    this.destroy();
                    self.$el.parentNode.removeChild(self.$el);
                } else if (this.activeIndex === 2) {
                    self.$emit('swipeLeft', pickedItem);
                    this.destroy();
                    self.$el.parentNode.removeChild(self.$el);
                }
            });
        },

        props: {
            picklistItem: Object,
        },

        computed: {
            elID() {
                return `picklist-item-${this.picklistItem.id}`;
            }
        }
    }
</script>

<style lang="scss" scoped>
    .col {
        background-color: #ffffff;
    }

    .header-row > div, .col {
        border: 1px solid #76777838;
    }

    .header-row > div {
        background-color: #76777838;
    }

    .swiper-container {
        overflow: hidden;
    }

    .swiper-slide {
        height: auto;
    }
</style>
