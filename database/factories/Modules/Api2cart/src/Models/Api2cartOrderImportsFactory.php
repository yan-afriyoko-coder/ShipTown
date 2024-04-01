<?php

namespace Database\Factories\Modules\Api2cart\src\Models;

use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use Illuminate\Database\Eloquent\Factories\Factory;

class Api2cartOrderImportsFactory extends Factory
{
    protected $model = Api2cartOrderImports::class;

    public function definition(): array
    {
        // this is a real example of the response from the API, copied raw
        return [
            'raw_import' => json_decode('
        {
           "id":"56962",
           "order_id":"56962",
           "basket_id":null,
           "customer":{
              "id":"71",
              "email":"seena289@gmail.com",
              "first_name":"Adam",
              "last_name":"Smith"
           },
           "create_at":{
              "value":"2015-11-26T08:25:31+0000",
              "format":"Y-m-d\\\\TH:i:sO"
           },
           "currency":{
              "id":"2",
              "name":"US Dollar",
              "iso3":"USD",
              "symbol_left":"$",
              "symbol_right":"",
              "rate":1,
              "avail":true,
              "default":true
           },
           "shipping_address":{
              "id":"2",
              "type":"shipping",
              "first_name":"Adam",
              "last_name":"Smith",
              "postcode":"12345",
              "address1":"Green str. 35",
              "address2":"",
              "phone":"",
              "city":"Chicago",
              "country":{
                 "code2":"US",
                 "code3":"USA",
                 "name":"United States of America"
              },
              "state":{
                 "code":"IL",
                 "name":"Illinois"
              },
              "company":"",
              "fax":"",
              "website":null,
              "gender":null,
              "region":null,
              "default":false,
              "additional_fields":{
                 "tax_id":null
              }
           },
           "billing_address":{
              "id":"1",
              "type":"billing",
              "first_name":"Adam",
              "last_name":"Smith",
              "postcode":"12345",
              "address1":"Green str. 35",
              "address2":"",
              "phone":"",
              "city":"Chicago",
              "country":{
                 "code2":"US",
                 "code3":"USA",
                 "name":"United States of America"
              },
              "state":{
                 "code":"IL",
                 "name":"Illinois"
              },
              "company":"",
              "fax":"",
              "website":null,
              "gender":null,
              "region":null,
              "default":true,
              "additional_fields":{
                 "tax_id":null
              }
           },
           "payment_method":{
              "name":""
           },
           "shipping_method":{
              "name":""
           },
           "shipping_methods":[
              {
                "name": "Dublin  [ Level 2, Dundrum Town Centre, Dublin, D16  ]\n",
                "additional_fields": {
                    "code": "pickupatstore_pickupatstore_3",
                    "provider_code": "pickupatstore"
                }
              }
           ],
           "status":{
              "id":"complete",
              "name":"Complete",
              "history":[
                 {
                    "id":"processing",
                    "name":"Processing",
                    "modified_time":{
                       "value":"2015-11-26T08:25:31+0000",
                       "format":"Y-m-d\\\\TH:i:sO"
                    },
                    "notify":true,
                    "comment":"Complete"
                 },
                 {
                    "id":"complete",
                    "name":"Complete",
                    "modified_time":{
                       "value":"2015-11-26T09:25:31+0000",
                       "format":"Y-m-d\\\\TH:i:sO"
                    },
                    "notify":true,
                    "comment":"Complete"
                 }
              ],
              "refund_info":null
           },
           "totals":{
              "total":23.56,
              "subtotal":267,
              "shipping":0,
              "tax":0,
              "discount":0
           },
           "total":{
              "subtotal_ex_tax":279,
              "wrapping_ex_tax":null,
              "shipping_ex_tax":0,
              "total_discount":0,
              "total_tax":0,
              "total":23.56,
              "total_paid":23.56,
              "additional_fields":{
                 "shipping_discount_ex_tax":null,
                 "subtotal_discount_ex_tax":null,
                 "tax_discount":null,
                 "subtotal_tax":0,
                 "wrapping_tax":null,
                 "shipping_tax":null
              }
           },
           "discounts":[

           ],
           "order_products":[
              {
                 "product_id":"8",
                 "order_product_id":null,
                 "model":"bag_01",
                 "name":"Bag",
                 "price":89,
                 "price_inc_tax":null,
                 "quantity":3,
                 "discount_amount":null,
                 "total_price":267,
                 "tax_percent":0,
                 "tax_value":0,
                 "tax_value_after_discount":null,
                 "options":[
                     {
                        "option_id": "string",
                        "name": "string",
                        "value": "string",
                        "price": 0,
                        "weight": 0,
                        "type": "string",
                        "product_option_value_id": "string",
                        "additional_fields": {},
                        "custom_fields": {}
                     }
                 ],
                 "variant_id":null,
                 "weight_unit":null,
                 "weight":0,
                 "parent_order_product_id":null
              },
              {
                 "product_id":"9",
                 "order_product_id":null,
                 "model":"box_05",
                 "name":"Box",
                 "price":12,
                 "price_inc_tax":null,
                 "quantity":1,
                 "discount_amount":null,
                 "total_price":12,
                 "tax_percent":0,
                 "tax_value":0,
                 "tax_value_after_discount":null,
                 "options":[

                 ],
                 "variant_id":null,
                 "weight_unit":null,
                 "weight":0,
                 "parent_order_product_id":null
              }
           ],
           "bundles":[

           ],
           "modified_at":{
              "value":"2015-11-26T09:25:31+0000",
              "format":"Y-m-d\\\\TH:i:sO"
           },
           "finished_time":null,
           "comment":"",
           "store_id":null,
           "warehouses_ids":[

           ],
           "refunds":[

           ]
        }
        ', true),
        ];
    }
}
