<?php


namespace Tests;


class ModelSample
{
    const PRODUCT = [
        "sku" => "123456",
        "name" => "Sample Product Name",
        "price" => 1.23,
        "quantity" => 10
    ];

    const PRODUCT_WITH_SALE_PRICE = [
        "sku" => "123456",
        "name" => "Sample Product Name",
        "price" => 1.23,
        "quantity" => 10,
        "sale_price" => 0.23,
        "sale_price_start_date" => '2019-01-30',
        "sale_price_end_date" => '2050-01-30',
    ];

}
