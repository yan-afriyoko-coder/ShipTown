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

                        });

                        data = data.filter(record => {
                            return (record[5] > 0) && (record[6] > 0);
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
                        "Qty Avail (RMS Web)",
                        "Qty Reserved (Web)",
                        "Qty Required",
                        "Qty Avail (RMS WH)",
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
