<?php

namespace Tests\Routes\Api;

use Tests\Routes\AuthenticatedRoutesTestCase;

class ProductsRoutesTest extends AuthenticatedRoutesTestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGet()
    {
        $response = $this->get('/api/products?' . 'include=inventory,aliases,tags');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'id',
                    'sku',
                    'name',
                    'price',
                    'sale_price',
                    'sale_price_start_date',
                    'sale_price_end_date',
                    'quantity',
                    'quantity_reserved',
                    'quantity_available',
                    'deleted_at',
                    'created_at',
                    'updated_at',
                    'inventory_source_shelf_location',
                    'inventory_source_quantity',
                    'inventory_source_product_id',
                    'inventory_source_location_id',
                    'inventory' => [
                        '*' => []
                    ],
                    'aliases' => [
                        '*' => []
                    ],
                    'tags' => [
                        '*' => []
                    ],
                ]
            ],
            'total',
        ]);
    }
}
