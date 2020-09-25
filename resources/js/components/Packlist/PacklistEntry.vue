<template>
    <div :id="getElementId" class="swiper-container">
        <div class="swiper-wrapper">

            <div class="swiper-slide error bg-success text-right">
                <div class="swipe-action-container swipe-action-container--right">
                    <div>SHIP THEM ALL</div>
                </div>
            </div>

            <div class="swiper-slide">
                <div class="row ml-1 mr-1">
                    <div class="col p-2 pl-3">
                        <entry-card :entry="picklistItem"/>
                    </div>
                </div>
            </div>

<!--            <div class="swiper-slide bg-warning" ></div>-->

        </div>
    </div>
</template>

<script>
    import 'swiper/css/swiper.min.css';
    import { Swiper } from 'swiper/js/swiper.esm.js';
    import EntryCard from "./EntryCard";

    export default {
        name: 'PacklistEntry',
        components: {EntryCard},
        mounted() {

            const swipedLeftIndex = 0;
            const swipedRightIndex = 2;

            const self = this;
            const pickedItem = this.picklistItem;

                // Initialize Swiper
            const swiper = new Swiper('#' + this.getElementId, {
                initialSlide: 1,
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
