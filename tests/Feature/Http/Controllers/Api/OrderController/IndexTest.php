<?php

namespace Tests\Feature\Http\Controllers\Api\OrderController;

use App\Models\Order;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\Passport;
use Spatie\Tags\Tag;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_has_tags_filter_exists()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        Order::query()->forceDelete();
        Tag::query()->forceDelete();

        $order = factory(Order::class)->create();

        $order->attachTag('Test');

        $response = $this->json('GET', '/api/orders?filter[has_tags]=Test&include=activities,activities.causer,shipping_address,order_shipments,order_products,order_products.product,order_products.product.aliases,packer,order_comments,order_comments.user');

        $this->assertEquals(1, $response->json()['meta']['total']);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_has_tags_filter_missing()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        Order::query()->forceDelete();
        Tag::query()->forceDelete();

        $order = factory(Order::class)->create();

        $response = $this->get('api/orders?filter[has_tags]=Test');

        $this->assertEquals(0, $response->json()['meta']['total']);
    }

    /** @test */
    public function test_index_call_returns_ok()
    {
        Order::query()->forceDelete();
        factory(Order::class)->create();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->getJson(route('orders.index'));

        $response->assertOk();

        $this->assertNotEquals(0, $response->json('meta.total'));

        $response->assertJsonStructure([
            'meta',
            'links',
            'data' => [
                '*' => [
                    'id',
                    'status_code',
                ],
            ],
        ]);
    }
}
