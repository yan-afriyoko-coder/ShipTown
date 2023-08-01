<?php

namespace Tests\Feature\Modules\InventoryReservations;

use App\Models\OrderStatus;
use App\Models\Product;
use App\Modules\InventoryReservations\src\EventServiceProviderBase;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class OrderCreatedTest.
 */
class OrderCreatedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'api');

        EventServiceProviderBase::enableModule();
    }

    /** @test */
    public function test_if_reserves_quantity_when_order_created()
    {
        OrderStatus::factory()->create([
            'code'           => 'new',
            'name'           => 'new',
            'order_active'   => true,
        ]);

        $product = Product::factory()->create();

        $this->assertDatabaseHas('products', ['quantity_reserved' => 0]);

        $quantityOrdered = rand(1, 30);

        $data = [
            'order_number' => '123456',
            'status_code'  => 'new',
            'products'     => [
                [
                    'sku'      => $product->sku,
                    'name'     => $product->name,
                    'quantity' => $quantityOrdered,
                    'price'    => $product->price,
                ],
            ],
        ];

        $response = $this->postJson('api/orders', $data);
        $response->assertOk();

        $this->assertDatabaseHas('inventory', ['quantity_reserved' => $quantityOrdered]);
    }
}
