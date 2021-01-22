<?php

namespace Tests\Feature\Http\Controllers\Api\Product;

use App\User;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\Product\ProductAliasController
 */
class ProductAliasControllerTest extends TestCase
{
    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->getJson(route('product.aliases.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'meta',
            'links',
            'data' => [
                '*' => [
                ]
            ]
        ]);
    }

    // test cases...
}
