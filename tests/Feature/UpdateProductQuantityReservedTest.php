<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateProductQuantityReservedTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Collection|Model|mixed
     */
    private $canceled_status;
    /**
     * @var Collection|Model|mixed
     */
    private $open_status;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var OrderProduct
     */
    private OrderProduct $orderProduct;

    protected function setUp(): void
    {
        parent::setUp();

        $this->canceled_status = factory(OrderStatus::class)->create([
            'code'           => 'canceled',
            'name'           => 'canceled',
            'reserves_stock' => false,
        ]);

        $this->open_status = factory(OrderStatus::class)->create([
            'code'           => 'open',
            'name'           => 'open',
            'reserves_stock' => true,
        ]);

        $this->order = factory(Order::class)->create([
            'status_code' => 'canceled',
        ]);

        $this->orderProduct = factory(OrderProduct::class)->create([])
            ->order()->associate($this->order);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_reserves_quantity()
    {
        $this->assertEquals(0, $this->orderProduct->quantity_reserved);

        $this->order->status_code = 'open';
        $this->order->save();

        $this->orderProduct->refresh();

        $this->markTestIncomplete();
//        $this->assertEquals($this->orderProduct->quantity_to_ship, $this->orderProduct->quantity_reserved);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_releases_reserved_quantity()
    {
        $this->order->status_code = 'open';
        $this->order->save();

        $this->order->status_code = 'canceled';
        $this->order->save();

        $this->orderProduct->refresh();

        $this->markTestIncomplete();
//        $this->assertEquals(0, $this->orderProduct->quantity_reserved);
    }
}
