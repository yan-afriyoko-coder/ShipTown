<template>
    <div :id="getElementId" class="swiper-container mb-3">
        <div class="swiper-wrapper">

            <div v-if="! disableSwipeRight" class="swiper-slide rounded bg-success text-right" >
                <div class="swipe-action-container swipe-action-container--right">
                    <div>SHIP THEM ALL</div>
                </div>
            </div>

            <div class="swiper-slide">
                <div class="row h-100">
                    <div class="col ml-1 mr-1 p-2 pl-3 rounded">
                        <slot name="content"></slot>
                    </div>
                </div>
            </div>

            <div v-if="! disableSwipeLeft" class="swiper-slide bg-warning rounded">
                <div class="swipe-action-container swipe-action-container--left text-black-50 font-weight-bold">
                    <div>SHIP PARTIAL / UNDO</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Swiper from "swiper";
// import Swiper styles
import 'swiper/swiper-bundle.css';

export default {
    name: 'SwipingCard',

    props: {
        disableSwipeLeft: false,
        disableSwipeRight: false,
    },

    mounted() {
        let swipedRightIndex = 0;
        let swipedLeftIndex = 2;
        let initialSlide = 1;

        if (this.disableSwipeRight) {
            initialSlide = 0;
            swipedRightIndex = -1;
            swipedLeftIndex = 1;
        }

        const self = this;

        // Initialize Swiper
        const swiper = new Swiper('#' + this.getElementId, {
            initialSlide: initialSlide,
            shortSwipes: false,
            longSwipes: true,
            longSwipesRatio: 0.30,
            longSwipesMs: 150,
            resistanceRatio: 0,
            speed: 150
        });


        // Event will be fired after transition
        swiper.on('transitionEnd', function() {
            console.log(this.activeIndex);
            if (this.activeIndex === swipedLeftIndex) {
                self.$emit('swipeLeft', []);

            } else if (this.activeIndex === swipedRightIndex) {
                self.$emit('swipeRight', []);
            }

            this.slideTo(initialSlide,0,false);
        });
    },

    computed: {
        getElementId() {
            return `swiping-card-${Math.floor(Math.random() * 10000000)}`;
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
