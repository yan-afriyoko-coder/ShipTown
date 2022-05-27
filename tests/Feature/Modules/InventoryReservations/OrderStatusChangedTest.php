<?php

namespace Tests\Feature\Modules\InventoryReservations;

use App\Models\OrderStatus;
use App\Models\Product;
use App\Modules\InventoryReservations\src\EventServiceProviderBase;
use App\User;
use Tests\TestCase;

class OrderStatusChangedTest extends TestCase
{
//    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');

        EventServiceProviderBase::enableModule();
    }

    /** @test */
    public function test_if_releases_quantity_when_status_changed()
    {
        factory(OrderStatus::class)->create([
            'code'           => 'open',
            'name'           => 'open',
            'order_active'   => true,
        ]);

        factory(OrderStatus::class)->create([
            'code'           => 'cancelled',
            'name'           => 'cancelled',
            'order_active'   => false,
        ]);

        $product = factory(Product::class)->create();

        $randomQuantity = rand(1, 30);

        $data = [
            'order_number' => '1234567',
            'status_code'  => 'open',
            'products'     => [
                [
                    'sku'      => $product->sku,
                    'name'     => $product->name,
                    'quantity' => $randomQuantity,
                    'price'    => $product->price,
                ],
            ],
        ];


        $response = $this->postJson('api/orders', $data);
        ray('response', $response->json());
        $response->assertOk();

        $this->assertDatabaseHas('inventory', ['quantity_reserved' => $randomQuantity]);
        $this->assertDatabaseHas('products', ['quantity_reserved' => $randomQuantity]);

        $order_id = $response->json('id');

        $response = $this->putJson('api/orders/'.$order_id, ['status_code' => 'cancelled']);
        ray('response', $response->json());
        $response->assertOk();

        $this->assertDatabaseHas('inventory', ['quantity_reserved' => 0]);
        $this->assertDatabaseHas('products', ['quantity_reserved' => 0]);
    }
}
