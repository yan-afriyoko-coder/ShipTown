<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Models\RmsapiConnection;
use App\Models\RmsapiProductImport;
use Faker\Generator as Faker;

$factory->define(RmsapiProductImport::class, function (Faker $faker) {

    $connection = factory(RmsapiConnection::class)->create();

    return [
        'connection_id' => $connection->id,
        'raw_import' => json_decode('{
           "id":1,
           "cost":110.34,
           "price":149.99,
           "active":1,
           "item_id":1,
           "price_a":144.99,
           "price_b":0,
           "price_c":0,
           "item_code":"11200",
           "sale_price":0,
           "category_id":9,
           "description":"R/C Glider",
           "is_web_item":0,
           "supplier_id":2,
           "date_created":"2003-06-03 17:17:23",
           "department_id":5,
           "reorder_point":0,
           "restock_level":0,
           "sale_end_date":null,
           "food_stampable":0,
           "db_change_stamp":1201,
           "sale_start_date":null,
           "quantity_on_hand":18,
           "quantity_on_order":0,
           "sub_description_1":"",
           "sub_description_2":"",
           "sub_description_3":"",
           "quantity_available":18,
           "quantity_committed":0,
           "quantity_discount_id":0
        }
    ')
    ];
});
