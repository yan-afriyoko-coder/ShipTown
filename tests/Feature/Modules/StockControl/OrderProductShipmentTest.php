<?php

namespace Tests\Feature\Modules\StockControl;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\OrderProduct;
use App\Models\OrderProductShipment;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\StockControl\src\StockControlServiceProvider;
use Tests\TestCase;

class OrderProductShipmentTest extends TestCase
{
    public function testExample()
    {
        StockControlServiceProvider::enableModule();

        /** @var Product $product */
        $product = factory(Product::class)->create();

        /** @var Warehouse $warehouse */
        $warehouse = factory(Warehouse::class)->create();

        /** @var OrderProduct $orderProduct */
        $orderProduct = factory(OrderProduct::class)->create();

        $orderShipment = OrderProductShipment::create([
            'warehouse_id' => $warehouse->id,
            'order_id' => $orderProduct->order_id,
            'order_product_id' => $orderProduct->id,
            'product_id' => $orderProduct->product_id,
            'quantity_shipped' => 4,
        ]);

        ray(InventoryMovement::all()->toArray());

        ray(Inventory::all()->toArray());

        $this->assertEquals(-4, $product->inventory($warehouse->code)->first()->quantity);
    }
}
