<template>
    <tr class="align-text-top bg-white ">
        <td class="text-nowrap">{{ order['order_number'] }}</td>
        <td class="text-nowrap">{{ order['status_code'] }}</td>
        <td class="text-nowrap text-right">{{ order['total'] }}</td>
        <td class="text-nowrap text-right">{{ order['total_paid'] }}</td>
        <td class="text-center text-nowrap">{{ order['product_line_count'] }}</td>
        <td class="text-center text-nowrap">{{ order['total_quantity_ordered'] }}</td>
        <td class="text-nowrap">{{ order['order_placed_at'] | moment('MM/DD H:mm') }}</td>
        <td class="text-center text-nowrap">{{ order['picked_at'] | moment('MM/DD H:mm') }}</td>
        <td class="text-center text-nowrap">{{ order['packed_at'] | moment('MM/DD H:mm') }}</td>
        <td class="text-left text-nowrap">{{ order['packer'] ? order['packer']['name'] : '' }}</td>
        <td class="text-nowrap">
            <template v-for="shipment in order['order_shipments']">
                <div>{{ shipment['shipping_number'] }}</div>
            </template>
        </td>
        <td class="text-nowrap">
            <template v-for="order_product in order['order_products']">
                <div class="mb-2">
                    <div>{{ order_product['name_ordered'] }}</div>
                    <div><a target="_blank" :href="'/products?sku=' + order_product['sku_ordered'] ">{{ order_product['sku_ordered'] }}</a></div>
                    <div>ordered: {{ order_product['quantity_ordered'] }}</div>
                    <div>picked: {{ order_product['quantity_picked'] }}</div>
                    <div>inventory: {{ getProductQuantity(order_product) }}</div>
                </div>
            </template>
        </td>
    </tr>
</template>

<script>
    export default {
        name: "OrderCard",

        props: {
            order: Object,
        },
        methods: {
            getProductQuantity(orderProduct) {
                return orderProduct['product'] ? orderProduct['product']['quantity'] : '-';
            },
        }
    }
</script>

<style scoped>
    .col {
        background-color: #ffffff;
        border: 0px solid #76777838;
    }

    tr {
        border: 1px solid #76777838;
        border-bottom: 15px solid #ffffff;
    }
</style>
