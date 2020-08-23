<?php

namespace Tests\Unit\Jobs\Rmsapi;

use App\Jobs\Rmsapi\ProcessImportedProductsJob;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductAlias;
use App\Models\RmsapiProductImport;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class ProcessImportedProductsJobTest extends TestCase
{
    public function testIfImportsAliases()
    {
        Event::fake();

        // prepare
        RmsapiProductImport::query()->delete();
        Product::query()->forceDelete();
        ProductAlias::query()->forceDelete();

        $importData = factory(RmsapiProductImport::class)->create();

        // act
        $job = new ProcessImportedProductsJob();

        $job->handle();

        // assert
        $this->assertTrue(ProductAlias::query()->exists(), 'Product aliases were not imported');
    }

    public function testIfProcessesCorrectly()
    {
        Event::fake();

        // prepare
        RmsapiProductImport::query()->delete();
        Product::query()->forceDelete();
        Inventory::query()->forceDelete();

        $importData = factory(RmsapiProductImport::class)->create();

        // act
        $job = new ProcessImportedProductsJob();

        $job->handle();


        // get product
        $product = Product::query()->where(['sku' => $importData->raw_import['item_code']])->first('id');

        $this->assertNotEmpty($product, 'Product does not exists');

        $exists = RmsapiProductImport::query()->whereNull('when_processed')->exists();
        $this->assertFalse($exists, 'when_processed is not updated');

        $exists = RmsapiProductImport::query()->whereNull('sku')->exists();
        $this->assertFalse($exists, 'sku column is not populated');

        $exists = RmsapiProductImport::query()->whereNull('product_id')->exists();
        $this->assertFalse($exists, 'product_id column is not populated');

        $wasInventoryUpdated = Inventory::query()
            ->where('product_id', '=', $product->id)
            ->where('quantity', '=', $importData->raw_import['quantity_on_hand'])
            ->where('quantity_reserved', '=', $importData->raw_import['quantity_committed'])
            ->exists();

        $this->assertTrue($wasInventoryUpdated, 'Inventory not updated correctly');
    }
}
