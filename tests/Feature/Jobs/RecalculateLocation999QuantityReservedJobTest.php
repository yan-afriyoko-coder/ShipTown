<?php

namespace Tests\Feature\Jobs;

use App\Jobs\Inventory\RecalculateLocation999QuantityReservedJob;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class RecalculateLocation999QuantityReservedJobTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Activity::query()->forceDelete();
        Product::query()->forceDelete();
        Inventory::query()->forceDelete();
        Order::query()->forceDelete();

        factory(Product::class, 10)->create();

        factory(Order::class)->with('orderProducts', 2)->create(['status_code' => 'processing']);

        Inventory::query()->update(['quantity_reserved' => 0]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
//        $incorrectProducts = Queries::getProductsWithIncorrectQuantityReservedQuery(999);
//        $this->assertTrue($incorrectProducts->exists());

        RecalculateLocation999QuantityReservedJob::dispatchNow();

        $this->markTestIncomplete();

//        $incorrectProducts = Queries::getProductsWithIncorrectQuantityReservedQuery(999);
//        $this->assertFalse($incorrectProducts->exists());
    }
}
