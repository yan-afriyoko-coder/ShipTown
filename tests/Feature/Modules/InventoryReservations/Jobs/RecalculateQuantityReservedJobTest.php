<?php

namespace Tests\Feature\Modules\InventoryReservations\Jobs;

use App\Models\Inventory;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Modules\InventoryReservations\src\EventServiceProviderBase;
use App\Modules\InventoryReservations\src\Jobs\RecalculateQuantityReservedJob;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class OrderCreatedTest.
 */
class RecalculateQuantityReservedJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');

        EventServiceProviderBase::enableModule();
    }

    /** @test */
    public function test_if_recalculates_correctly()
    {
        factory(OrderStatus::class)->create([
            'code'          => 'new',
            'name'          => 'new',
            'order_active'  => true,
        ]);

        $product = factory(Product::class)->create();

        $random_quantity = rand(1, 20);

        $data = [
            'order_number' => '1234',
            'status_code'  => 'new',
            'products'     => [
                [
                    'sku'      => $product->sku,
                    'name'     => $product->name,
                    'quantity' => $random_quantity,
                    'price'    => $product->price,
                ],
            ],
        ];

        $this->postJson('api/orders', $data)->assertOk();

        Inventory::query()->update(['quantity_reserved' => 0]);
        Product::query()->update(['quantity_reserved' => 0]);

        RecalculateQuantityReservedJob::dispatchNow();

        $this->assertDatabaseHas('inventory', ['quantity_reserved' => $random_quantity]);
        $this->assertDatabaseHas('products', ['quantity_reserved' => $random_quantity]);
    }
}
