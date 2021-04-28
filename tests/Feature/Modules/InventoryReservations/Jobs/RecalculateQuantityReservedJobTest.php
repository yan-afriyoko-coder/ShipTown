<?php

namespace Tests\Feature\Modules\InventoryReservations\Jobs;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Modules\InventoryReservations\src\Jobs\RecalculateQuantityReservedJob;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class OrderCreatedTest
 * @package Tests\Feature\Modules\InventoryReservations
 */
class RecalculateQuantityReservedJobTest extends TestCase
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
    public function test_if_recalculates()
    {
        factory(OrderStatus::class)->create([
            'code' => 'new',
            'name' => 'new',
            'reserves_stock' => true,
        ]);

        $order = factory(Order::class)->make(['status_code' => 'new']);

        $orderProduct = factory(OrderProduct::class)->make();

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

        Inventory::query()->update(['quantity_reserved' => 0]);
        Product::query()->update(['quantity_reserved' => 0]);

        RecalculateQuantityReservedJob::dispatchNow();

        $this->assertDatabaseHas('inventory', ['quantity_reserved' => $orderProduct->quantity_ordered]);
        $this->assertDatabaseHas('products', ['quantity_reserved' => $orderProduct->quantity_ordered]);
    }
}
