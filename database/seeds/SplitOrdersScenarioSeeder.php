<?php

use App\Events\Order\ActiveOrderCheckEvent;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\Automations\src\Actions\Order\SplitOrderToWarehouseCodeAction;
use App\Modules\Automations\src\Conditions\Order\StatusCodeEqualsCondition;
use App\Modules\Automations\src\Models\Action;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class SplitOrdersScenarioSeeder extends Seeder
{
    /**
     * @var Collection|Model|mixed
     */
    private $sampleProducts;

    /**
     * @var Collection|Warehouse
     */
    private $sampleWarehouses;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->ensurePackingStatusExists();
        $this->createSampleWarehouses(3);
        $this->createWarehouseAutomations();
        $this->createSampleProducts(3);
        $this->createSampleSplitOrders(1);
        $this->createSampleSplitSingleProductsOrders(1);
    }

    /**
     */
    private function createWarehouseAutomations(): void
    {
        $this->sampleWarehouses->each(function (Warehouse $warehouse) {
            $status_code_name = 'packing_' . $warehouse->code;

            $automation = new Automation();
            $automation->enabled = false;
            $automation->name = 'packing to ' . $status_code_name;
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
    }

    private function ensurePackingStatusExists(): void
    {
        factory(OrderStatus::class)->create(['name' => 'packing', 'code' => 'packing']);
    }

    /**
     * @param int $count
     * @return Collection|Model|mixed
     */
    private function createSampleWarehouses(int $count)
    {
        $this->sampleWarehouses = factory(Warehouse::class, $count)->create();

        return $this->sampleWarehouses;
    }

    /**
     * @param int $count
     */
    private function createSampleProducts(int $count)
    {
        $this->sampleProducts = factory(Product::class, $count)->create();
    }

    /**
     *
     */
    private function createSampleSplitOrders($count)
    {
        /** @var Order $order */
        $orders = factory(Order::class, $count)->make();

        $orders->each(function (Order $order) {
            $order->status_code = 'packing';
            $order->order_number .= 'SPLIT';
            $order->save();

            // we will duplicate collection each time
            $warehouses = collect($this->sampleWarehouses);

            $this->sampleProducts->each(function (Product $product) use ($order, $warehouses) {
                $warehouse = $warehouses->shift();

                Inventory::updateOrCreate([
                    'product_id' => $product->getKey(),
                    'warehouse_id' => $warehouse->getKey(),
                    'location_id' => $warehouse->code,
                ], [
                    'quantity' => 100
                ]);

                $orderProduct = new OrderProduct();
                $orderProduct->order_id = $order->getKey();
                $orderProduct->product_id = $product->getKey();
                $orderProduct->name_ordered = $product->name;
                $orderProduct->sku_ordered = $product->sku;
                $orderProduct->price = $product->price;
                $orderProduct->quantity_ordered = 1;
                $orderProduct->save();
            });
        });
    }

    private function createSampleSplitSingleProductsOrders(int $count)
    {
        /** @var Order $order */
        $orders = factory(Order::class, $count)->make();

        $orders->each(function (Order $order) {
            $order->status_code = 'packing';
            $order->order_number .= 'SPLITSINGLE';
            $order->save();

            // we will duplicate collection each time
            $warehouses = collect($this->sampleWarehouses);

            $this->sampleProducts->each(function (Product $product) use ($order, $warehouses) {

                $warehouse = $warehouses->first();

                Inventory::updateOrCreate([
                    'product_id' => $product->getKey(),
                    'warehouse_id' => $warehouse->getKey(),
                    'location_id' => $warehouse->code,
                ], [
                    'quantity' => 3
                ]);

                $warehouse = $warehouses->last();

                Inventory::updateOrCreate([
                    'product_id' => $product->getKey(),
                    'warehouse_id' => $warehouse->getKey(),
                    'location_id' => $warehouse->code,
                ], [
                    'quantity' => 3
                ]);

                $orderProduct = new OrderProduct();
                $orderProduct->order_id = $order->getKey();
                $orderProduct->product_id = $product->getKey();
                $orderProduct->name_ordered = $product->name;
                $orderProduct->sku_ordered = $product->sku;
                $orderProduct->price = $product->price;
                $orderProduct->quantity_ordered = 5;
                $orderProduct->save();
            });
        });
    }
}
