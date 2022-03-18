<?php

namespace Tests\Unit\Services;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testIfUpdatesProductsQuantitiesAreCorrect()
    {
        $orderFake = factory(Order::class)->make();
        $randomCount = rand(1, 10);

        OrderAddress::query()->forceDelete();
        Order::query()->forceDelete();

        $attributes = [
            'order_number'     => $orderFake->order_number,
            'status_code'      => $orderFake->status_code,
            'total'            => 12.40,
            'total_paid'       => 12.40,
            'order_products'   => factory(OrderProduct::class, 2)->make()->toArray(),
            'shipping_address' => factory(OrderAddress::class)->make()->toArray(),
        ];

        $quantityExpected = collect($attributes['order_products'])->sum(function ($orderProduct) {
            return $orderProduct['quantity_ordered'];
        });

        $order = OrderService::updateOrCreate($attributes);
        $this->assertEquals($quantityExpected, OrderProduct::sum('quantity_ordered'));

        // we will run one update and see if quantities are updated correctly

        $attributes['order_products'] = array_merge(
            $attributes['order_products'],
            factory(OrderProduct::class, $randomCount)->make()->toArray()
        );

        $quantityExpected = collect($attributes['order_products'])->sum(function ($orderProduct) {
            return $orderProduct['quantity_ordered'];
        });

        $order = OrderService::updateOrCreate($attributes);
        $this->assertEquals($quantityExpected, OrderProduct::sum('quantity_ordered'));
    }

    public function testIfUpdatesProductsCorrectly()
    {
        $orderFake = factory(Order::class)->make();
        $randomCount = rand(1, 10);

        OrderAddress::query()->forceDelete();
        Order::query()->forceDelete();

        $attributes = [
            'order_number'     => $orderFake->order_number,
            'status_code'      => $orderFake->status_code,
            'total'            => 12.40,
            'total_paid'       => 12.40,
            'order_products'   => factory(OrderProduct::class, 2)->make()->toArray(),
            'shipping_address' => factory(OrderAddress::class)->make()->toArray(),
        ];

        $order = OrderService::updateOrCreate($attributes);
        $this->assertEquals(2, OrderProduct::count());

        $attributes['order_products'] = array_merge(
            $attributes['order_products'],
            factory(OrderProduct::class, $randomCount)->make()->toArray()
        );

        $order = OrderService::updateOrCreate($attributes);
        $equals = 2 + $randomCount === OrderProduct::count();

        //        if (!$equals) {
        //            dd($attributes);
        //        }

        $this->assertTrue($equals);
    }

    public function testStatusCodeUpdate()
    {
        // some automations listening to event might
        // automatically change status
        // this way we disable it
        Event::fake();

        $orderFake = factory(Order::class)->make();

        OrderAddress::query()->forceDelete();
        Order::query()->forceDelete();

        $attributes = [
            'order_number'     => $orderFake->order_number,
            'status_code'      => $orderFake->status_code,
            'total'            => 12.40,
            'total_paid'       => 12.40,
            'order_products'   => factory(OrderProduct::class, 2)->make()->toArray(),
            'shipping_address' => factory(OrderAddress::class)->make()->toArray(),
        ];

        $order = OrderService::updateOrCreate($attributes);

        $this->assertNotNull($order);

        $this->assertEquals($orderFake->status_code, $order->status_code);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testSimplestCreation()
    {
        OrderAddress::query()->forceDelete();
        OrderProduct::query()->forceDelete();
        Order::query()->forceDelete();

        $order = factory(Order::class)->make();

        $attributes = [
            'order_number'     => $order->order_number,
            'order_products'   => [],
            'shipping_address' => [],
        ];

        $order = OrderService::updateOrCreate($attributes);

        $this->assertNotNull($order);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCanFulfillMethod()
    {
        Order::query()->forceDelete();
        Product::query()->forceDelete();
        Inventory::query()->forceDelete();

        /** @var Warehouse $warehouse */
        $warehouse = factory(Warehouse::class)->create();
        factory(Product::class)->create();

        $order = factory(Order::class)
            ->with('orderProducts')
            ->create();

        $orderProduct = $order->orderProducts()->first();

        $inventory = Inventory::query()->updateOrCreate([
            'product_id'  => $orderProduct->product_id,
            'location_id' => $warehouse->code,
        ], [
            'warehouse_code'    => $warehouse->code,
            'quantity'          => $orderProduct->quantity_ordered,
            'quantity_reserved' => 0,
        ]);

        $this->assertTrue(OrderService::canFulfill($order), 'a');
        $this->assertFalse(OrderService::canNotFulfill($order), 'b');

        $this->assertTrue(OrderService::canFulfill($order, $warehouse->code), 'c');
        $this->assertFalse(OrderService::canFulfill($order, 99), 'd');
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCanNotFulfillMethod()
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
            'product_id'  => $orderProduct->product_id,
            'location_id' => 100,
        ], [
            'quantity'          => 0,
            'quantity_reserved' => 0,
        ]);

        $this->assertTrue(OrderService::canNotFulfill($order), 'Should not be able to fulfill from any location');
        $this->assertTrue(OrderService::canNotFulfill($order, 99), 'Should not be able to fulfill from location 99');
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
            'product_id'  => $orderProduct->product_id,
            'location_id' => $locationId,
        ], [
            'quantity' => $orderProduct->quantity_ordered - 1,
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
            'product_id'  => $orderProduct->product_id,
            'location_id' => $locationId,
        ], [
            'quantity' => $orderProduct->quantity_ordered,
        ]);

        $this->assertTrue(
            OrderService::canFulfill($order, $locationId)
        );
    }
}
