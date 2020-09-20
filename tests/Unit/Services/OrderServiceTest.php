<?php

namespace Tests\Unit\Services;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testNullLocationId()
    {
        Order::query()->forceDelete();
        Product::query()->forceDelete();
        Inventory::query()->forceDelete();

        factory(Product::class)->create();

        $order = factory(Order::class)
            ->with('orderProducts')
            ->create();

        $orderProduct = $order->orderProducts()->first();

        Inventory::query()->updateOrCreate([
            'product_id' => $orderProduct->product_id,
            'location_id' => 100
        ], [
            'quantity' => 0,
            'quantity_reserved' => 0,
        ]);

        $canFulfill = OrderService::canFulfill($order);

        $this->assertFalse($canFulfill, 'Should not be able to fulfill, no stock (canFulfill)');
        $this->assertTrue(OrderService::canNotFulfill($order), 'Should not be able to fulfill, no stock (canNotFulfill)');

        Inventory::query()->updateOrCreate([
            'product_id' => $orderProduct->product_id,
            'location_id' => 100
        ], [
            'quantity' => $orderProduct->quantity_ordered,
            'quantity_reserved' => 0,
        ]);

        $this->assertTrue(OrderService::canFulfill($order));
        $this->assertFalse(OrderService::canNotFulfill($order));

        $this->assertTrue(OrderService::canFulfill($order, 100));
        $this->assertFalse(OrderService::canFulfill($order, 99));
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testFailedCanFulfill()
    {
        $locationId = rand(1, 100);

        $order = factory(Order::class)
            ->with('orderProducts', 1)
            ->create();

        $orderProduct = $order->orderProducts()->first();

        Inventory::updateOrCreate([
            'product_id' => $orderProduct->product_id,
            'location_id' => $locationId,
        ], [
            'quantity' => $orderProduct->quantity_ordered - 1
        ]);

        $this->assertFalse(
            OrderService::canFulfill($order, $locationId)
        );
    }

    public function testSuccessfulCanFulfill()
    {
        $locationId = rand(1, 100);

        $order = factory(Order::class)
            ->with('orderProducts', 1)
            ->create();

        $orderProduct = $order->orderProducts()->first();

        Inventory::updateOrCreate([
            'product_id' => $orderProduct->product_id,
            'location_id' => $locationId,
        ], [
            'quantity' => $orderProduct->quantity_ordered
        ]);

        $this->assertTrue(
            OrderService::canFulfill($order, $locationId)
        );
    }
}
