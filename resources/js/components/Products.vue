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
                        var data = response.data.data;

                        data = data.filter(record => record.inventory.length > 0);

                        data = data.map(record => {
                            const inventory_list = record.inventory;
                            record['inventory'] = [];

                            record['inventory']['99'] = inventory_list.filter(inventory => inventory.location_id = 99)[0];
                            record['inventory']['100'] = inventory_list.filter(inventory => inventory.location_id = 100)[0];

                            return record;
                        });

                        data = data.map(record => {
                            return [
                                record.sku,// sku
                                record.name, // product name
                                record.category, // category
                                record.inventory['100'] ? record.inventory['100'].quantity_available: 0, // quantity available in web
                                record.inventory['100'] ? record.inventory['100'].quantity_reserved: 0, // quantity ord (web)
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
                        "Qty Avail (Web)",
                        "Qty Ord (Web)",
                        "Qty Req (WH)",
                        "Qty Avail (WH)"
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
