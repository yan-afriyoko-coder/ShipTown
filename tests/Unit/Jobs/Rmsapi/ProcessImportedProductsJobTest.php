<?php

namespace Tests\Unit\Jobs\Rmsapi;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductAlias;
use App\Models\Warehouse;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedProductRecordsJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Spatie\Tags\Tag;
use Tests\TestCase;

class ProcessImportedProductsJobTest extends TestCase
{
    use RefreshDatabase;

    public function testIfAddsAvailableOnlineTag()
    {
        // prepare
        RmsapiProductImport::query()->forceDelete();
        Product::query()->forceDelete();
        ProductAlias::query()->forceDelete();
        Tag::query()->forceDelete();

        $importData = RmsapiProductImport::factory()->create();

        $raw_import = $importData->raw_import;
        $raw_import['is_web_item'] = 1;
        $importData->update(['raw_import' => $raw_import]);

        // do
        foreach (RmsapiConnection::all() as $rmsapiConnection) {
            ProcessImportedProductRecordsJob::dispatch($rmsapiConnection->id);
        }

        // assert
        $this->assertEquals(
            Product::firstOrFail()->tags()->exists(),
            $importData->raw_import['is_web_item'],
            'Available Online tag not imported'
        );
    }

    public function testIfImportsAliases()
    {
        // prepare
        RmsapiProductImport::query()->forceDelete();
        Product::query()->forceDelete();
        ProductAlias::query()->forceDelete();

        RmsapiProductImport::factory()->create();

        // do
        foreach (RmsapiConnection::all() as $rmsapiConnection) {
            ProcessImportedProductRecordsJob::dispatch($rmsapiConnection->id);
        }

        // assert
        $this->assertTrue(ProductAlias::query()->exists(), 'Product aliases were not imported');
    }

    public function testIfProcessesCorrectly()
    {
        // prepare
        RmsapiProductImport::query()->forceDelete();
        Product::query()->forceDelete();
        Inventory::query()->forceDelete();

//        Warehouse::factory()->create();
        $importData = RmsapiProductImport::factory()->create();

        // do
        foreach (RmsapiConnection::all() as $rmsapiConnection) {
            ProcessImportedProductRecordsJob::dispatch($rmsapiConnection->id);
        }

        // get product
        $product = Product::query()->where(['sku' => $importData->raw_import['item_code']])->first('id');

        $this->assertNotEmpty($product, 'Product does not exists');

        $exists = RmsapiProductImport::query()->whereNull('when_processed')->exists();
        $this->assertFalse($exists, 'when_processed is not updated');

        $exists = RmsapiProductImport::query()->whereNull('sku')->exists();
        $this->assertFalse($exists, 'sku column is not populated');

        $exists = RmsapiProductImport::query()->whereNull('product_id')->exists();
        $this->assertFalse($exists, 'product_id column is not populated');
    }
}
