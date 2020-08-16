<template>
    <div class="row text-left">
        <div class="col-md-8">
            <div class="text-primary h4">{{ entry['name_ordered'] }}</div>
            <div class="text-secondary h5">sku: <span class="font-weight-bold"> {{ entry['sku_ordered'] }} </span></div>
            <div class="text-secondary h5">product: <span class="font-weight-bold"> {{ entry.product['sku'] }} </span></div>
        </div>
        <div class="col-md-4">
            <div class="row pt-1 mt-1 text-center border-top">
                <div class="col-4">
                    <div class="">Qty Packed</div>
                    <div class="h3">{{ Math.ceil(entry['quantity_packed']) }}</div>
                </div>
                <div class="col-4">
                    <div>To Pick</div>
                    <div class="h3">{{ Math.ceil(entry['quantity_requested']) }}</div>
                </div>
                <div class="col-4">
                    <div class="">Shelf</div>
                    <div class="h3">{{ entry['inventory_source_shelf_location'] }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "EntryCard",

        props: {
            entry: Object,
        },
    }
</script>

<style scoped>

</style>
