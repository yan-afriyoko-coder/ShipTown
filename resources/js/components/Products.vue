<template>
        <hot-table :data="data" :settings="settings"></hot-table>
</template>

<script>
    import { HotTable } from '@handsontable/vue';

    export default {
        data: function() {
            return {
                data: [
                    ["164965","15cm stainless Steel milk pan","HARDWARE",1,7,6],
                    ["133423","Doorstopper white 5pcs","HARDWARE",2,3,1],
                    ["120850","Teapot 100oz","HARDWARE",3,5,2],
                    ["146502","18cm Non Stick Sauce Pan & Gla","HARDWARE",5,9,4],
                    ["110943","BUTTERFLY CAN OPEN","HARDWARE",2,12,10],
                    ["110942","ICECREAM SCOOP CHROME PLATED","HARDWARE",1,6,5],
                    ["126020","DESSERT FORK 4pk","HARDWARE",1,2,1],
                    ["126373","Safety pins 120pcs","HARDWARE",4,6,2],
                    ["110939","S/S Gravy Boat","HARDWARE",1,12,11],
                    ["132652","HWARE DOG LEASH 3M J19E","HARDWARE",1,8,7],
                    ["132023","FARMHOUSE RAMEKIN 9.5 X 5CM /","HARDWARE",2,8,6],
                    ["116805","DESSERT KNIFE 4pk","HARDWARE",1,8,7],
                    ["117088","Kettle Coloured 3.5L","HARDWARE",6,8,2]
                ],
                settings: {
                    licenseKey: 'non-commercial-and-evaluation',
                    colHeaders: ["SKU", "Name", "Category", "Qty Avail (Web)", "Qty Ord (Web)", "Qty Req (WH)"],
                    rowHeaders: false,
                },
            };
        },
        components: {
            HotTable
        }
    }
</script>
