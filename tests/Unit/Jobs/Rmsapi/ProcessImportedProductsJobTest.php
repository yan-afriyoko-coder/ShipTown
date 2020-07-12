<?php

namespace Tests\Unit\Jobs\Rmsapi;

use App\Jobs\Rmsapi\ProcessImportedProductsJob;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\RmsapiProductImport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class ProcessImportedProductsJobTest extends TestCase
{
    public function test_if_processes_correctly()
    {
        // prepare
        RmsapiProductImport::query()->delete();
        Product::query()->delete();
        Inventory::query()->delete();

        $importData = factory(RmsapiProductImport::class)->create();

        // act
        $job = new ProcessImportedProductsJob();

        $job->handle();


        // get product
        $product = Product::query()
            ->where('sku','=', $importData->raw_import['item_code'])
            ->first('id');

        $this->assertNotEmpty($product, 'Product does not exists');

        $this->assertFalse(
            RmsapiProductImport::query()->whereNull('when_processed')->exists(),
            'when_processed is not updated'
        );

        $this->assertFalse(
            RmsapiProductImport::query()->whereNull('sku')->exists(),
            'sku column is not populated'
        );

        $this->assertFalse(
            RmsapiProductImport::query()->whereNull('product_id')->exists(),
            'product_id column is not populated'
        );

        $wasInventoryUpdated = Inventory::query()
            ->where('product_id','=', $product->id)
            ->where('quantity','=', $importData->raw_import['quantity_on_hand'])
            ->where('quantity_reserved','=', $importData->raw_import['quantity_committed'])
            ->exists();

        $this->assertTrue($wasInventoryUpdated, 'Inventory not updated correctly');

    }
}
