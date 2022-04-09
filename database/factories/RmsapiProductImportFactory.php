<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(RmsapiProductImport::class, function (Faker $faker) {
    $connection = factory(RmsapiConnection::class)->create();

    $quantity_on_hand = random_int(0, 1000);
    $quantity_committed = random_int(0, $quantity_on_hand);
    $quantity_available = $quantity_on_hand - $quantity_committed;
    $reorder_point = random_int(0, 100);
    $restock_level = random_int(0, $reorder_point);

    return [
        'connection_id' => $connection->id,
        'raw_import'    => json_decode('{
           "id":1,
           "cost":110.34,
           "price":'.(random_int(1, 100000) / 100).',
           "active":1,
           "item_id":1,
           "price_a":0,
           "price_b":0,
           "price_c":0,
           "item_code":"11200",
           "sale_price":0,
           "category_id":9,
           "description":"R/C Glider",
           "is_web_item":'.random_int(0, 1).',
           "supplier_id":2,
           "date_created":"2003-06-03 17:17:23",
           "department_id":5,
           "reorder_point":'. $reorder_point .',
           "restock_level":'. $restock_level .',
           "sale_end_date":null,
           "food_stampable":0,
           "db_change_stamp":'.random_int(1000, 100000).',
           "sale_start_date":null,
           "quantity_on_hand":'.$quantity_on_hand.',
           "quantity_on_order":0,
           "sub_description_1":"",
           "sub_description_2":"",
           "sub_description_3":"",
           "quantity_available":'.$quantity_available.',
           "quantity_committed":'.$quantity_committed.',
           "quantity_discount_id":0,
           "rmsmobile_shelve_location":"'.Str::upper($faker->randomLetter).$faker->numberBetween(0, 9).'",
           "aliases": [
                {
                    "id": 1,
                    "alias": "'.(string) $faker->unique()->randomNumber(6).'-alias",
                    "item_id": 1
                },
                {
                    "id": 2,
                    "alias": "'.(string) $faker->unique()->randomNumber(6).'-alias",
                    "item_id": 1
                }
           ]
        }
    '),
    ];
});
