<template>
    <div :id="getElementId" class="swiper-container mb-3">
        <div class="swiper-wrapper">

            <div class="swiper-slide">
                <div class="row ml-1 mr-1 disabled">
                    <div class="col p-2 pl-3">
                        <entry-card :entry="picklistItem"/>
                    </div>
                </div>
            </div>

            <div class="swiper-slide bg-warning">
                <div class="swipe-action-container swipe-action-container--left text-black-50 font-weight-bold">
                    <div>RESTORE</div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    import 'swiper/css/swiper.min.css';
    import { Swiper } from 'swiper/js/swiper.esm.js';
    import EntryCard from "./EntryCard";

    export default {
        name: 'PackedEntry',

        components: {EntryCard},

        props: {
            picklistItem: Object,
        },

        mounted() {
            const self = this;
            const pickedItem = this.picklistItem;

            // Initialize Swiper
            const swiper = new Swiper('#' + this.getElementId, {
                initialSlide: 0,
                resistanceRatio: 0,
                speed: 150
            });

            // Event will be fired after transition
            swiper.on('transitionEnd', function() {
                    self.$emit('swipeLeft', pickedItem);
            });

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
