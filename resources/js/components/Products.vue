<template>
    <div class="container">
        <div v-if="productsData.total > productsData.per_page" class="row">
            <ul class="pagination">
                <li :class="{
                    'page-item': true,
                    'disabled': productsData.current_page == 1
                }">
                    <a class="page-link" href="#" aria-label="Previous" @click.prevent="prev">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#" aria-label="Next">
                        {{ productsData.from }} - {{ productsData.to }} of {{ productsData.total }}
                    </a>
                </li>
                <li :class="{
                    'page-item': true,
                    'disabled': productsData.current_page == productsData.last_page
                }">
                    <a class="page-link" href="#" aria-label="Next" @click.prevent="next">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="row">
            <hot-table :data="tableData" :settings="settings"></hot-table>
        </div>
        <div class="row" style="margin-top: 10px">
            <pagination :data="productsData" @pagination-change-page="loadProductList"></pagination>
        </div>
    </div>
</template>

<script>
    import { HotTable } from '@handsontable/vue';

    import loadingOverlay from '../mixins/loading-overlay';

    export default {
        mixins: [loadingOverlay],

        components: {
            HotTable
        },

        created: function() {
            this.loadProductList(1);
        },

        methods: {
            loadProductList: function(page) {
                this.showLoading();
                axios.get(`/api/products?page=${page}`).then(({ data }) => {
                    this.productsData = data;
                }).then(this.hideLoading);
            },

            next() {
                this.loadProductList(this.productsData.current_page + 1);
            },

            prev() {
                this.loadProductList(this.productsData.current_page -1);
            }
        },

        data: function() {
            return {
                productsData: {
                    data: [],
                    total: 0,
                    per_page: 100,
                },
                settings: {
                    licenseKey: 'non-commercial-and-evaluation',
                    colHeaders: [
                        'SKU',
                        'Description',
                        'Qty',
                        'Qty Reserved',
                        'Price',
                        'Sale Price',
                        'Sale Start Date',
                        'Sale End Date',
                    ],
                    rowHeaders: false,
                },
            };
        },

        computed: {
            tableData() {
                return this.productsData.data.map((product => {
                    return [
                        product.sku,
                        product.name,
                        product.quantity,
                        product.quantity_reserved,
                        product.price,
                        product.sale_price,
                        product.sale_price_start_date,
                        product.sale_price_end_date
                    ];
                }));
            }
        }
    }
</script>
