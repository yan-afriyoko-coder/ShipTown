<template>
        <div class="swiper-container mb-3">
            <div class="swiper-wrapper">

                <div class="swiper-slide error bg-success text-right">
                    <div class="swipe-action-container swipe-action-container--right">
                        <div>PICK ALL</div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="row ml-1 mr-1">
                        <div class="col p-2 pl-3">
                            <div class="row text-left">
                                <div class="col-md-8">
                                    <div class="text-primary h5">{{ pick['name_ordered'] }}</div>
                                    <div class="">sku: <span class="font-weight-bold"> {{ pick['sku_ordered'] }} </span></div>
                                    <div class="text-secondary">product: <span class="font-weight-bold"> <a target="_blank" :href="'/products?sku=' + product_sku ">{{ product_sku }}</a> </span></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row text-center">
                                        <div class="col-6 text-left">
                                            <div class="text-secondary">stock: <span class="font-weight-bold"> {{ Math.ceil(pick['inventory_source_quantity']) }} </span></div>
                                        </div>
                                        <div class="col-2" v-bind:class="{ 'bg-warning': Math.ceil(pick['total_quantity_to_pick']) > 1 }">
                                            <div class="small">to pick</div>
                                            <div class="h3 red">{{ this.quantity_requested_integer }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="small">shelf</div>
                                            <div class="h3">{{ pick['inventory_source_shelf_location'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide bg-warning">
                    <div class="swipe-action-container swipe-action-container--left text-black-50 font-weight-bold">
                        <div>PARTIAL PICK</div>
                    </div>
                </div>

            </div>
        </div>


</template>

<script>
import {Swiper} from "swiper/js/swiper.esm";

export default {
    name: "PickCard",

    props: {
        pick: Object,
    },

    data() {
        return {
            swiper: null,
        }
    },

    computed: {
        quantity_requested_integer() {
            return Math.ceil(this.pick['total_quantity_to_pick']);
        },
        product_sku() {
            return  this.pick['product'] ? this.pick['product']['sku'] : '';
        },
    },

    mounted() {
        this.swiper = new Swiper('#' + this.$attrs.id, {
            initialSlide: 1,
            shortSwipes: false,
            longSwipes: true,
            longSwipesRatio: 0.30,
            longSwipesMs: 150,
            resistanceRatio: 0,
            speed: 150
        });

        this.swiper.on('transitionEnd', this.transitionEnd);
    },

    methods: {
        transitionEnd() {
            if (this.swiper.activeIndex === 1) {
                return;
            }

            if (this.swiper.activeIndex === 0) {
                this.$emit('swipeRight', this.pick);
            }

            if (this.swiper.activeIndex === 2) {
                this.$emit('swipeLeft', this.pick);
            }

            this.swiper.slideTo(1,0,false);
        },
    }

}
</script>

<style scoped>
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
