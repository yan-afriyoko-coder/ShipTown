<template>
    <div class="row p-0 h-100">
        <div class="col-12 col-lg-4 align-text-top">
            <product-info-card :product= "record['product']"></product-info-card>
        </div>

        <div class="row col-sm-12 col-lg-8 text-right mt-1">
            <div class="col-12 col-md-6">
                <table class="table-borderless small text-left text-nowrap">
                    <tr>
                        <td>unique id:</td>
                        <td class="pl-1">{{ record['custom_unique_reference_id'] }}</td>
                    </tr>
                    <tr>
                            <td>movement id:</td>
                        <td class="pl-1">{{ record['id'] }}</td>
                    </tr>
                    <tr>
                            <td>at:</td>
                        <td class="pl-1">{{ record['occurred_at'] | moment('YYYY MMM D H:mm') }}</td>
                    </tr>
                    <tr>
                        <td>type:</td>
                        <td class="pl-1"><a href="" @click="setUrlParameter('filter[description]', record['description'] )">{{ record['description'] }}</a></td>
                    </tr>
                    <tr>
                        <td>by:</td>
                        <td class="pl-1">{{ record['user'] ? record['user']['name'] : '' }}</td>
                    </tr>
                    <tr>
                        <td>shelf:</td>
                        <td class="pl-1">{{ record['inventory']['shelf_location'] }}</td>
                    </tr>
                    <tr>
                        <td>in stock:</td>
                        <td class="pl-1">{{ dashIfZero(Number(record['inventory']['quantity'])) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-12 col-md-6 text-right align-text-top h-100">
                <text-card label="warehouse" :text="record['inventory']['warehouse_code']" class="fa-pull-left"></text-card>
                <number-card label="before" :number="record['quantity_before']"></number-card>
                <number-card label="change" :number="record['quantity_delta']"></number-card>
                <number-card label="after" :number="record['quantity_after']"></number-card>
            </div>
        </div>
    </div>
</template>


<script>

import helpers from "./../../mixins/helpers";
import url from "../../mixins/url";

export default {
    mixins: [helpers, url],
    props: {
        record: {
            type: Object,
            required: true
        }
    }
}
</script>
