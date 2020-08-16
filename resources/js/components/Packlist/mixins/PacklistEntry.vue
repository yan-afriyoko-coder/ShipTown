<template>
    <div :id="getElementId" class="swiper-container">
        <div class="swiper-wrapper">

            <div class="swiper-slide bg-success"></div>

            <div class="swiper-slide">
                <div class="row ml-1 mr-1" :class=" picklistItem['is_packed'] ? 'disabled' : '' ">
                    <div class="col p-2 pl-3">
                        <div class="row text-left">
                            <div class="col-md-8">
                                <div class="text-primary h4">{{ picklistItem['name_ordered'] }}</div>
                                <div class="text-secondary h5">sku: <span class="font-weight-bold"> {{ picklistItem['sku_ordered'] }} </span></div>
                                <div class="text-secondary h5">product: <span class="font-weight-bold"> {{ picklistItem.product['sku'] }} </span></div>
                            </div>
                            <div class="col-md-4">
                                <div class="row pt-1 mt-1 text-center border-top">
                                    <div class="col-4">
                                        <div class="">Qty Packed</div>
                                        <div class="h3">{{ Math.ceil(picklistItem['quantity_packed']) }}</div>
                                    </div>
                                    <div class="col-4">
                                        <div>To Pick</div>
                                        <div class="h3">{{ Math.ceil(picklistItem['quantity_requested']) }}</div>
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

            <div class="swiper-slide bg-warning" ></div>

        </div>
    </div>
</template>

<script>
    import 'swiper/css/swiper.min.css';
    import { Swiper } from 'swiper/js/swiper.esm.js';

    export default {
        name: 'PacklistEntry',
        mounted() {

            const swipedLeftIndex = 0;
            const swipedRightIndex = 2;

            const self = this;
            const pickedItem = this.picklistItem;

            const initialSlide = pickedItem['is_packed'] ? 0 : 1;

                // Initialize Swiper
            const swiper = new Swiper('#' + this.getElementId, {
                initialSlide: initialSlide,
                resistanceRatio: 0,
                speed: 150
            });


            // Event will be fired after transition
            swiper.on('transitionEnd', function() {

                if (this.activeIndex === swipedLeftIndex) {
                    self.$emit('swipeRight', pickedItem);

                } else if (this.activeIndex === swipedRightIndex) {
                    self.$emit('swipeLeft', pickedItem);
                }

                this.slideTo(1,0,false);
            });


        },

        props: {
            picklistItem: Object,
        },

        computed: {
            getElementId() {
                return `picklist-item-${this.picklistItem.id}`;
            }
        },



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

    .disabled > div {
        opacity: 0.5;
    }

    .swiper-container {
        overflow: hidden;
    }

    .swiper-slide {
        height: auto;
    }
</style>
