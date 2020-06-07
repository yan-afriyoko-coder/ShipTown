<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Api2cartOrderImport::class, function (Faker $faker) {
    return [
        'raw_import' =>'
            {
                "id": "0",
                "order_id": "0",
                "basket_id": null,
                "customer": {
                    "id": "9",
                    "email": "email",
                    "first_name": "name",
                    "last_name": "apellido"
                },
                "create_at": {
                    "value": "2014-02-18T22:00:53+0000",
                    "format": "Y-m-d\\TH:i:sO"
                },
                "currency": {
                    "id": "2",
                    "name": "US Dollar",
                    "iso3": "USD",
                    "symbol_left": "$",
                    "symbol_right": "",
                    "rate": 1,
                    "avail": true,
                    "default": true
                },
                "shipping_address": {
                    "id": "2",
                    "type": "shipping",
                    "first_name": "nameFactura",
                    "last_name": "apelliidoFactura",
                    "postcode": "-25282",
                    "address1": "direccionFactura",
                    "address2": "",
                    "phone": "tlfBill",
                    "city": "cityBill",
                    "country": {
                        "code2": "",
                        "code3": "",
                        "name": "paisBill"
                    },
                    "state": {
                        "code": "",
                        "name": "stateBill"
                    },
                    "company": "companyBill",
                    "fax": "faBill",
                    "website": null,
                    "gender": null,
                    "region": null,
                    "default": false,
                    "additional_fields": {
                        "tax_id": null
                    }
                },
                "billing_address": {
                    "id": "1",
                    "type": "billing",
                    "first_name": "nameFactura",
                    "last_name": "apelliidoFactura",
                    "postcode": "-25282",
                    "address1": "direccionFactura",
                    "address2": "",
                    "phone": "tlfBill",
                    "city": "cityBill",
                    "country": {
                        "code2": "",
                        "code3": "",
                        "name": "paisBill"
                    },
                    "state": {
                        "code": "",
                        "name": "stateBill"
                    },
                    "company": "companyBill",
                    "fax": "faBill",
                    "website": null,
                    "gender": null,
                    "region": null,
                    "default": true,
                    "additional_fields": {
                        "tax_id": null
                    }
                },
                "payment_method": {
                    "name": ""
                },
                "shipping_method": {
                    "name": ""
                },
                "shipping_methods": [
                    {
                        "name": ""
                    }
                ],
                "status": {
                    "id": "2",
                    "name": "Processing",
                    "history": [
                        {
                            "id": "2",
                            "name": "Processing",
                            "modified_time": {
                                "value": "2014-02-18T02:00:56+0000",
                                "format": "Y-m-d\\TH:i:sO"
                            },
                            "notify": true,
                            "comment": "Processing"
                        }
                    ],
                    "refund_info": null
                },
                "totals": {
                    "total": 43.98,
                    "subtotal": 43.98,
                    "shipping": 0,
                    "tax": 0,
                    "discount": 0
                },
                "total": {
                    "subtotal_ex_tax": 43.98,
                    "wrapping_ex_tax": null,
                    "shipping_ex_tax": 0,
                    "total_discount": 0,
                    "total_tax": 0,
                    "total": 43.98,
                    "total_paid": null,
                    "additional_fields": {
                        "shipping_discount_ex_tax": null,
                        "subtotal_discount_ex_tax": null,
                        "tax_discount": null,
                        "subtotal_tax": 0,
                        "wrapping_tax": null,
                        "shipping_tax": null
                    }
                },
                "discounts": [],
                "order_products": [],
                "bundles": [],
                "modified_at": {
                    "value": "2014-02-26T13:06:49+0000",
                    "format": "Y-m-d\\TH:i:sO"
                },
                "finished_time": null,
                "comment": "",
                "store_id": null,
                "warehouses_ids": [],
                "refunds": []
            }'
    ];
});
