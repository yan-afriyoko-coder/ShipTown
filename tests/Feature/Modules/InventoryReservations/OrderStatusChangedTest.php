<?php

namespace Tests\Feature\Modules\InventoryReservations;

use App\Models\Inventory;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use App\Models\Product;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStatusChangedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');

        OrderProduct::query()->forceDelete();
        Inventory::query()->forceDelete();
        Product::query()->forceDelete();
        OrderStatus::query()->forceDelete();
    }

    /** @test */
    public function test_if_releases_quantity_when_status_changed()
    {
        factory(OrderStatus::class)->create([
            'code'           => 'open',
            'name'           => 'open',
            'reserves_stock' => true,
        ]);

        factory(OrderStatus::class)->create([
            'code'           => 'cancelled',
            'name'           => 'cancelled',
            'reserves_stock' => false,
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

        $response = $this->postJson('api/orders', $data)->assertOk();
        $this->assertDatabaseHas('inventory', ['quantity_reserved' => $randomQuantity]);
        $this->assertDatabaseHas('products', ['quantity_reserved' => $randomQuantity]);

        $order_id = $response->json('id');
        $this->putJson('api/orders/'.$order_id, ['status_code' => 'cancelled'])
            ->assertOk();

        $this->assertDatabaseHas('inventory', ['quantity_reserved' => 0]);
        $this->assertDatabaseHas('products', ['quantity_reserved' => 0]);
    }
}
