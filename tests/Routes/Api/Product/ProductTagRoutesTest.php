<?php

namespace Tests\Routes\Api\Product;

use Spatie\Tags\Tag;
use Tests\Routes\AuthenticatedRoutesTestCase;

class ProductTagRoutesTest extends AuthenticatedRoutesTestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGet()
    {
        factory(Tag::class)->create();

        $response = $this->get('/api/product/tags');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'order_column',
                ]
            ],
            'links',
            'meta',
        ]);
    }
}
