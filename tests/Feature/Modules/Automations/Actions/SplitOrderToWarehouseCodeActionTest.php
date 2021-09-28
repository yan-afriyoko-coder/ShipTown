<?php

namespace Tests\Feature\Modules\Automations\Actions;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Jobs\FireActiveOrderCheckEventForAllActiveOrdersJob;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\Automations\src\Actions\Order\SplitOrderToWarehouseCodeAction;
use App\Modules\Automations\src\AutomationsServiceProvider;
use App\Modules\Automations\src\Conditions\Order\StatusCodeEqualsCondition;
use App\Modules\Automations\src\Models\Action;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SplitOrderToWarehouseCodeActionTest extends TestCase
{
    use RefreshDatabase;

//    public function test_when_splitting_product_not_possible()
//    {
//        // when ordered is 10 but only 8 available in total
//        // we should change status
//        $this->markAsRisky();
//    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $random_number = rand(2,4);

        AutomationsServiceProvider::enableModule();

        $products = factory(Product::class, $random_number)->create();
        $warehouses = factory(Warehouse::class, $random_number)->create();

        $warehouses->each(function (Warehouse $warehouse) use ($products) {
            $product = $products->shift();
            Inventory::updateOrCreate([
                'product_id' => $product->getKey(),
                'warehouse_id' => $warehouse->getKey(),
            ],[
                'quantity' => 100,
                'location_id' => $warehouse->code,
            ]);
        });

        /** @var  $order */
        $order = factory(Order::class)->create(['status_code' => 'packing']);

        Product::all()->each(function (Product $product) use ($order) {
            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $order->getKey();
            $orderProduct->product_id = $product->getKey();
            $orderProduct->name_ordered = $product->name;
            $orderProduct->sku_ordered = $product->sku;
            $orderProduct->price = $product->price;
            $orderProduct->quantity_ordered = 1;
            $orderProduct->save();
        });

        $warehouses->each(function (Warehouse $warehouse) {
            $status_code_name = 'packing_'.$warehouse->code;

            $automation = new Automation();
            $automation->enabled = false;
            $automation->name = 'packing to '.$status_code_name;
            $automation->event_class = ActiveOrderCheckEvent::class;
            $automation->save();

            $condition = new Condition();
            $condition->automation_id = $automation->getKey();
            $condition->condition_class = StatusCodeEqualsCondition::class;
            $condition->condition_value = 'packing';
            $condition->save();

            $action = new Action();
            $action->automation_id = $automation->getKey();
            $action->action_class = SplitOrderToWarehouseCodeAction::class;
            $action->action_value = $warehouse->code;
            $action->save();

            $automation->enabled = true;
            $automation->save();
        });

        FireActiveOrderCheckEventForAllActiveOrdersJob::dispatch();

        // we will have original order left + X new ones
        $this->assertEquals(1 + $random_number, Order::count());
        $this->assertEquals($random_number * 2, OrderProduct::count());
    }
}
