<?php

namespace Tests\Feature\Jobs\Maintenance;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Modules\Maintenance\src\Jobs\Products\RecalculateProductQuantityReservedJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecalculateProductQuantityReservedJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $product = factory(Product::class)->create();

        Inventory::updateOrCreate([
            'product_id' => $product->getKey(),
            'location_id' => rand(1,20)
        ], [
            'quantity_reserved' => rand(10,100)
        ]);

        $product->update([
            'quantity_reserved' => 0,
        ]);

        RecalculateProductQuantityReservedJob::dispatchNow();

        $this->assertEquals(Inventory::sum('quantity_reserved'), Product::sum('quantity_reserved'));
    }
}
