<?php

namespace Tests\Feature\Modules\InventoryReservations;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use App\Models\Product;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class OrderCreatedTest
 * @package Tests\Feature\Modules\InventoryReservations
 */
class OrderCreatedTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_if_reserves_quantity_when_order_created()
    {
        factory(OrderStatus::class)->create([
            'code' => 'new',
            'name' => 'new',
            'reserves_stock' => true,
        ]);

        $order = factory(Order::class)->make(['status_code' => 'new']);

        $orderProduct = factory(OrderProduct::class)->make();

        $this->assertDatabaseHas('products', ['quantity_reserved' => 0]);

        $data = [
            'order_number' => $order->order_number,
            'status_code' => $order->status_code,
            'products' => [
                [
                    'sku' => $orderProduct->sku_ordered,
                    'name' => $orderProduct->name_ordered,
                    'quantity' => $orderProduct->quantity_ordered,
                    'price' => $orderProduct->price,
                ]
            ]
        ];

        $this->postJson('api/orders', $data)->assertOk();

        $this->assertDatabaseHas('inventory', ['quantity_reserved' => $orderProduct->quantity_ordered]);
        $this->assertDatabaseHas('products', ['quantity_reserved' => $orderProduct->quantity_ordered]);
    }
}
