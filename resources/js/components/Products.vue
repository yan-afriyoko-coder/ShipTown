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
                        var data = response.data;

                        data = data.map(record => {
                            return [
                                record.sku,// sku
                                record.name, // product name
                                record.category, // category
                                record.inventory['100'] ? record.inventory['100'].quantity: 0, // quantity on hand in web
                                record.inventory['100'] ? record.inventory['100'].quantity_reserved: 0, // quantity available in web
                                record.inventory['100'] ? record.inventory['100'].quantity_available: 0, // quantity available in web
                                record.inventory['100'] ? record.inventory['100'].quantity - record.inventory['100'].quantity_reserved:'', // quantity required from warehouse
                                record.inventory['99'] ? record.inventory['99'].quantity_available:'', // quantity available in warehous
                            ];
                        });

                        this.data = data;
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
                        "Qty On Hand (Web)",
                        "Qty Reserved (Web)",
                        "Qty Avail (Web)",
                        "Qty Req",
                        "Qty Avail (WH)",
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
