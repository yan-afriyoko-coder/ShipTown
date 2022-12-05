<?php

namespace Tests\Feature\Modules\Automations\Conditions;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\Automations\src\Conditions\Order\CanFulfillFromLocationCondition;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use function factory;

class CanFulfillFromLocationConditionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_not_fulfill_from_location_0_condition()
    {
        /** @var Warehouse $warehouse */
        $warehouse = Warehouse::factory()->create();

        /** @var Product $product */
        $product = Product::factory()->create();

        /** @var Automation $automation */
        $automation = Automation::factory()->create([
            'enabled' => true,
            'name' => 'paid to can_fulfill',
        ]);

        /** @var Condition $condition */
        $condition = $automation->conditions()->create([
            'condition_class' => CanFulfillFromLocationCondition::class,
            'condition_value' => '0' // we are using location_id = 0 for ALL locations
        ]);

        /** @var Order $order */
        $order = Order::factory()->create([]);

        /** @var OrderProduct $orderProduct */
        $orderProduct = OrderProduct::factory()->create(['order_id' => $order->getKey()]);

        Inventory::updateOrCreate([
            'product_id' => $product->getKey(),
            'location_id' => $warehouse->code
        ], [
            'quantity' => $orderProduct->quantity_ordered - 1,
        ]);

        $this->assertFalse($condition->isTrue($order));
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_fulfill_from_location_0_condition()
    {
        /** @var Warehouse $warehouse */
        $warehouse = Warehouse::factory()->create();

        /** @var Product $product */
        $product = Product::factory()->create();

        /** @var Automation $automation */
        $automation = Automation::factory()->create([
            'enabled' => true,
            'name' => 'paid to can_fulfill',
        ]);

        /** @var Condition $condition */
        $condition = $automation->conditions()->create([
            'condition_class' => CanFulfillFromLocationCondition::class,
            'condition_value' => '0' // we are using location_id = 0 for ALL locations
        ]);

        /** @var Order $order */
        $order = Order::factory()->create([]);

        /** @var OrderProduct $orderProduct */
        $orderProduct = OrderProduct::factory()->create(['order_id' => $order->getKey()]);

        Inventory::updateOrCreate([
            'product_id' => $product->getKey(),
            'location_id' => $warehouse->code
        ], [
            'quantity' => 100,
        ]);

        $this->assertTrue($condition->isTrue($order));
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_not_fulfill_condition()
    {
        /** @var Warehouse $warehouse */
        $warehouse = Warehouse::factory()->create();

        /** @var Product $product */
        $product = Product::factory()->create();

        /** @var Automation $automation */
        $automation = Automation::factory()->create([
            'enabled' => true,
            'name' => 'paid to can_fulfill',
        ]);

        /** @var Condition $condition */
        $condition = $automation->conditions()->create([
            'condition_class' => CanFulfillFromLocationCondition::class,
            'condition_value' => $warehouse->code
        ]);

        Inventory::updateOrCreate([
            'product_id' => $product->getKey(),
            'location_id' => $warehouse->code
        ], [
            'quantity' => 0,
        ]);

        /** @var Order $order */
        $order = Order::factory()->create([]);
        OrderProduct::factory()->create(['order_id' => $order->getKey()]);

        $this->assertFalse($condition->isTrue($order));
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_fulfill_valid_condition()
    {
        /** @var Warehouse $warehouse */
        $warehouse = Warehouse::factory()->create();

        /** @var Product $product */
        $product = Product::factory()->create();

        /** @var Automation $automation */
        $automation = Automation::factory()->create([
            'enabled' => true,
            'name' => 'paid to can_fulfill',
        ]);

        /** @var Condition $condition */
        $condition = $automation->conditions()->create([
            'condition_class' => CanFulfillFromLocationCondition::class,
            'condition_value' => $warehouse->code
        ]);

        Inventory::updateOrCreate([
            'product_id' => $product->getKey(),
            'location_id' => $warehouse->code
        ], [
            'quantity' => 100,
        ]);

        /** @var Order $order */
        $order = Order::factory()->create([]);
        OrderProduct::factory()->create(['order_id' => $order->getKey()]);

        $this->assertTrue($condition->isTrue($order));
    }
}
