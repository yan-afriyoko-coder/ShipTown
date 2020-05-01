<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <input class="form-control" @keyup.enter="handleSearchEnter" @blur="loadProductList(1)" v-model="query" placeholder="Search for products..." />
            </div>
            <div class="col">
                <ul v-if="productsData.total > productsData.per_page" class="pagination">
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
        </div>
        <div class="row">
            <hot-table ref="hotTable" :data="tableData" :settings="settings"></hot-table>
        </div>
        <div v-if="productsData.total == 0" class="row" >
            <div class="col">
                <div class="alert alert-info" role="alert">
                    No products found.
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 10px">
            <pagination :data="productsData" @pagination-change-page="loadProductList"></pagination>
        </div>
    </div>
</template>

<script>
    import { HotTable } from '@handsontable/vue';

    import loadingOverlay from '../mixins/loading-overlay';

    const colHeaderMap = [
        { col: 'sku', label: 'SKU' },
        { col: 'name', label: 'Description' },
        { col: 'quantity', label: 'Qty' },
        { col: 'quantity_reserved', label: 'Qty Reserved' },
        { col: 'price', label: 'Price' },
        { col: 'sale_price', label: 'Sale Price' },
        { col: 'sale_price_start_date', label: 'Sale Start Date' },
        { col: 'sale_price_end_date', label: 'Sale End Date' },
    ];

    export default {
        mixins: [loadingOverlay],

        components: {
            HotTable
        },

        created() {
            this.loadProductList(1);
        },

        mounted() {
            this.$refs.hotTable.hotInstance.addHook('beforeColumnSort', (currentSortConfig, destinationSortConfigs) => {
                if (destinationSortConfigs.length > 0) {
                    let column = colHeaderMap[destinationSortConfigs[0].column];
                    this.sort = column.col;
                    this.order = destinationSortConfigs[0].sortOrder;
                } else {
                    this.sort = null;
                    this.order = null;
                }

                this.loadProductList(1);
            });
        },

        methods: {
            loadProductList: function(page) {
                this.showLoading();
                axios.get('/api/products', {
                    params: {
                        page: page,
                        q: this.query,
                        sort: this.sort,
                        order: this.order,
                    }
                }).then(({ data }) => {
                    this.productsData = data;
                }).then(this.hideLoading);
            },

            next() {
                console.log(this.productsData.current_page);
                this.loadProductList(this.productsData.current_page + 1);
            },

            prev() {
                this.loadProductList(this.productsData.current_page -1);
            },

            handleSearchEnter(event) {
                event.target.blur();
            }
        },

        data: function() {
            return {
                query: null,
                sort: null,
                order: null,
                productsData: {
                    data: [],
                    total: 0,
                    per_page: 100,
                    current_page: 1,
                },
                settings: {
                    licenseKey: 'non-commercial-and-evaluation',
                    colHeaders: (index) => {
                        return colHeaderMap[index].label
                    },
                    rowHeaders: false,
                    columnSorting: true
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
