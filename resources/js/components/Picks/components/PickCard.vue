<template>
        <div :id="pickCardId" class="swiper-container mb-3">
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
                                    <div class="text-primary h4">{{ pick['name_ordered'] }}</div>
                                    <div class="text-secondary h5">sku: <span class="font-weight-bold"> {{ pick['sku_ordered'] }} </span></div>
<!--                                    <div class="text-secondary h5">product: <span class="font-weight-bold"> {{ product_sku }} </span></div>-->
                                </div>
                                <div class="col-md-4">
                                    <div class="row pt-1 mt-1 text-center border-top">
                                        <div class="col-6">
                                            <div class=""></div>
<!--                                            <div class="text-secondary h6 text-left">order: <span class="font-weight-bold"> {{ pick['order']['order_number'] }} </span></div>-->
<!--                                            <div class="text-secondary h6 text-left">lines: <span class="font-weight-bold"> {{ pick['order']['product_line_count'] }} </span></div>-->
<!--                                            <div class="text-secondary h6 text-left">stock: <span class="font-weight-bold"> {{ Math.ceil(picklistItem['inventory_source_quantity']) }} </span></div>-->
                                        </div>
                                        <div class="col-2">
                                            <div>To Pick</div>
<!--                                            <div class="h3">{{ quantity_requested_integer }}</div>-->
                                        </div>
                                        <div class="col-4">
                                            <div class="">Shelf</div>
<!--                                            <div class="h3">{{ picklistItem['inventory_source_shelf_location'] }}</div>-->
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


    mounted() {

        const swiper = new Swiper('#' + this.pickCardId, {
            initialSlide: 1,
            resistanceRatio: 0,
            speed: 150
        });

        const self = this;
        let pick = this.pick;

        swiper.on('transitionEnd', function () {

            if (this.activeIndex === 0) {
                self.$emit('swipeRight', pick);
            } else if (this.activeIndex === 2) {
                self.$emit('swipeLeft', pick);
            }

            this.slideTo(1,0,false);

        });
    },

    computed: {
        pickCardId() {
            return `pick-card-${this.pick['id']}`;
        }
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
