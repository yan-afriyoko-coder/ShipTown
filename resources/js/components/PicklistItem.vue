<template>
    <div :id="elID" lass="swiper-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide error"></div>
            <div class="swiper-slide">
                <div class="row mb-3">
                    <div class="col">
                        <div class="row text-center">
                            <div class="col-1">
                                <div class="row">
                                    <div class="col">{{ location_id }}</div>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="row">
                                    <div class="col">{{ shelve_location }}</div>
                                </div>
                            </div>
                            <div class="col-3 sku-col">
                                <div>
                                    {{ sku }}
                                </div>
                            </div>
                            <div class="col-4 text-left row-product-name">
                                <div>
                                    {{ name }}
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="row">
                                    <div class="col">{{ quantity_to_pick }}</div>
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
            this.quantity_to_pick = this.picklistItem.quantity_reserved;
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
            sku: null,
            quantity: null,
            name: null,
            quantity_reserved: null,
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

    .row-product-name .col {
        padding: 10px;
    }

    .sku-col {
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
