<template>
    <div class="row mb-3">
        <div class="col">
            <div class="row text-center">
                <div class="col-3">
                    <div class="row header-row">
                        <div class="col">LOC</div>
                    </div>
                    <div class="row">
                        <div class="col">{{ stock.location_id }}</div>
                    </div>
                </div>
                <div class="col-6 sku-col">
                    <div>
                        {{ product.sku }}
                    </div>
                </div>
                <div class="col-3">
                    <div class="row header-row">
                        <div class="col">QTY</div>
                    </div>
                    <div class="row">
                        <div class="col">{{ stock.quantity }}</div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center row-product-name">
                <div class="col text-center">{{ product.name }}</div>
            </div>
            <div class="row text-center">
                <div class="col-2">
                    <div class="row header-row">
                        <div class="col">PRICE</div>
                    </div>
                    <div class="row">
                        <div class="col">{{ product.price }}</div>
                    </div>
                </div>
                <div class="col-2">
                    <div class="row header-row">
                        <div class="col">SALE</div>
                    </div>
                    <div class="row">
                        <div class="col">{{ product.sale_price }}</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="row header-row">
                        <div class="col">START DATE</div>
                    </div>
                    <div class="row">
                        <div class="col">{{ product.sale_price_start_date | moment('YYYY.MM.DD') }}</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="row header-row">
                        <div class="col">END DATE</div>
                    </div>
                    <div class="row">
                        <div class="col">{{ product.sale_price_end_date | moment('YYYY.MM.DD') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            product: Object,
            stock: Object,
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
