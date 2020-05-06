<template>
    <div class="container">
        <div class="d-print-none">
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
        <div class="d-none d-print-block">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">SKU</th>
                        <th scope="col">Name</th>
                        <th scope="col">Category</th>
                        <th scope="col">Qty Avail (RMS Web)</th>
                        <th scope="col">Qty Reserved (Web)</th>
                        <th scope="col">Qty Required</th>
                        <th scope="col">Qty Avail (RMS WH)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, i) in allProducts" :key="i">
                        <td>{{ row[0] }}</td>
                        <td>{{ row[1] }}</td>
                        <td>{{ row[2] }}</td>
                        <td>{{ row[3] }}</td>
                        <td>{{ row[4] }}</td>
                        <td>{{ row[5] }}</td>
                        <td>{{ row[6] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>


<script>
    import { HotTable } from '@handsontable/vue';

    import loadingOverlay from '../mixins/loading-overlay';

    export default {
        mixins: [loadingOverlay],

        created: function() {
            this.loadProductList(1);
            this.loadAllProducts();
        },

        methods: {
            loadProductList: function(page) {
                this.showLoading();
                axios.get(`/api/inventory?page=${page}`).then(({ data }) => {
                    this.productsData = data;
                }).then(this.hideLoading);
            },

            loadAllProducts: function(page) {
                axios.get(`/api/inventory?per_page=all`).then(({ data }) => {
                    this.allProducts = data.map(this.dataMap);
                });
            },

            next() {
                this.loadProductList(this.productsData.current_page + 1);
            },

            prev() {
                this.loadProductList(this.productsData.current_page -1);
            },

            dataMap: (record) => {
                let quantity_available_rms_web = record.inventory['100'] ? record.inventory['100'].quantity_available: 0;
                let quantity_reserved_web = record.inventory['999'] ? record.inventory['999'].quantity_reserved: 0;
                let quantity_required = quantity_reserved_web > quantity_available_rms_web ? quantity_reserved_web - quantity_available_rms_web: 0;
                let quantity_available_warehouse = record.inventory['99'] ? record.inventory['99'].quantity_available: 0;

                return [
                    record.sku,
                    record.name,
                    record.category,
                    quantity_available_rms_web,
                    quantity_reserved_web,
                    quantity_required,
                    quantity_available_warehouse,
                ];
            }
        },

        data: function() {
            return {
                allProducts: [],
                productsData: {
                    data: [],
                    total: 0,
                    per_page: 100,
                },
                settings: {
                    licenseKey: 'non-commercial-and-evaluation',
                    colHeaders: [
                        "SKU",
                        "Name",
                        "Category",
                        "Qty Avail (RMS Web)",
                        "Qty Reserved (Web)",
                        "Qty Required",
                        "Qty Avail (RMS WH)",
                    ],
                    rowHeaders: false,
                },
            };
        },

        computed: {
            tableData() {
                return this.productsData.data.map(this.dataMap);
            }
        },

        components: {
            HotTable
        }
    }
</script>
