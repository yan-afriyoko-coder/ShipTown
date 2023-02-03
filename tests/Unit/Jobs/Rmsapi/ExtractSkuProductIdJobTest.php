<?php

namespace Tests\Unit\Jobs\Rmsapi;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\Rmsapi\src\Jobs\ImportSalesJob;
use App\Modules\Rmsapi\src\Jobs\ImportProductsJob;
use App\Modules\Rmsapi\src\Jobs\ImportShippingsJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedProductRecordsJob;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ExtractSkuProductIdJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfAllSkuArePopulated()
    {
//        Event::fake();

        // prepare
        Product::query()->forceDelete();
        Inventory::query()->delete();

        RmsapiProductImport::query()->delete();

        Warehouse::factory()->create();

        RmsapiProductImport::factory()->count(5)->create([
            'sku'            => null,
            'product_id'     => null,
            'when_processed' => null,
        ]);

        // do
        foreach (RmsapiConnection::all() as $rmsapiConnection) {
            ProcessImportedProductRecordsJob::dispatch($rmsapiConnection->id);
        }

        // assert
        $this->assertFalse(
            RmsapiProductImport::query()
                ->whereNotNull('when_processed')
                ->whereNull('sku')
                ->exists(),
            'sku column is not populated'
        );

        $this->assertFalse(
            RmsapiProductImport::query()
                ->whereNotNull('when_processed')
                ->whereNull('product_id')
                ->exists(),
            'product_id column is not populated'
        );
    }
}
