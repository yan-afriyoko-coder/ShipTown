<template>
        <hot-table :data="data" :settings="settings"></hot-table>
</template>

<script>
    import { HotTable } from '@handsontable/vue';

    export default {
        created: function() {
            this.loadProductList();
        },

        methods: {
            loadProductList: function() {
                axios.get('/api/inventory')
                    .then(response => {
                        this.data = response.data.map(record => {
                            return [
                                record.sku,// sku
                                record.name, // product name
                                record.category, // category
                                null, // TODO: quantity available web
                                record.quantity_reserved, // quantity reserved (on web orders)
                                null, // TODO: quantity required from warehouse
                            ];
                        });
                    });
            },
        },

        data: function() {
            return {
                data: null,
                settings: {
                    licenseKey: 'non-commercial-and-evaluation',
                    colHeaders: [
                        "SKU",
                        "Name",
                        "Category",
                        "Qty Avail (Web)",
                        "Qty Ord (Web)",
                        "Qty Req (WH)"
                    ],
                    rowHeaders: false,
                },
            };
        },

        components: {
            HotTable
        }
    }
</script>
