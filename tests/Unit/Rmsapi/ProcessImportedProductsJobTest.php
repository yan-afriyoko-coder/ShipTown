<?php

namespace Tests\Unit\Rmsapi;

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


        // checks

        // check if all imports were processed and when_processed updated
        $unprocessedOrdersExists = RmsapiProductImport::query()
            ->whereNull('when_processed')
            ->exists();

        $this->assertFalse($unprocessedOrdersExists, 'Some products still not processed');

        // check product creation
        $product = Product::query()->where('sku','=', $importData->raw_import['item_code'])->first('id');

        $this->assertNotEmpty($product, 'Product does not exists');

        // check inventory update
        $inventoryUpdated = Inventory::query()
            ->where('product_id','=', $product->id)
            ->where('quantity','=', $importData->raw_import['quantity_on_hand'])
            ->where('quantity_reserved','=', $importData->raw_import['quantity_committed'])
            ->exists();

        $this->assertTrue($inventoryUpdated, 'Inventory not updated correctly');

    }
}
