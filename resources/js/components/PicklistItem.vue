<template>
    <div :id="elID" lass="swiper-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide error"></div>
            <div class="swiper-slide">
                <div class="row mb-3 ml-1 mr-1">
                    <div class="col p-2 pl-3">
                        <div class="row text-left">
                            <div class="col-md-8">
                                <div class="text-primary h4">{{ name }}</div>
                                <div class="text-secondary h5">sku: <span class="font-weight-bold"> {{ sku }}</span></div>
                            </div>
                            <div class="col-md-4">
                                <div class="row pt-1 mt-1 text-center border-top">
                                    <div class="col-6">
                                        <div class=""></div>
                                        <div class="text-secondary h6 text-left">order: <span class="font-weight-bold"> - </span></div>
                                    </div>
                                    <div class="col-3">
                                        <div>To Pick</div>
                                        <div class="h3">{{ quantity_to_pick }}</div>
                                    </div>
                                    <div class="col-3">
                                        <div class="">Shelf</div>
                                        <div class="h3">{{ shelve_location }}</div>
                                    </div>
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
    import 'swiper/css/swiper.min.css';
    import { Swiper } from 'swiper/js/swiper.esm.js';

    export default {
        created() {
            this.location_id = this.picklistItem.location_id;
            this.shelve_location = this.picklistItem.shelve_location;
            this.sku = this.picklistItem.product.sku;
            this.name = this.picklistItem.product.name;
            this.quantity_to_pick = Math.ceil(this.picklistItem.quantity_to_pick);
        },

        mounted() {
            const self = this;
            // Initialize Swiper
            const swiper = new Swiper('#' + this.elID, {
                initialSlide: 1,
                resistanceRatio: 0,
                speed: 150
            });

            // Event will be fired after transition
            swiper.on('transitionEnd', function () {
                if (this.activeIndex === 0) {
                    self.$emit('transitionEnd', {
                        id: self.picklistItem.id,
                        quantity_picked: self.quantity_to_pick,
                    });
                    this.destroy();
                    self.$el.parentNode.removeChild(self.$el);
                }
            });
        },

        props: {
            picklistItem: Object,
        },

        data: () => ({
            location_id: null,
            shelve_location: null,
            quantity_to_pick: null,
        }),

        methods: {

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
</style>
