<?php

namespace Tests\Unit\Jobs\Rmsapi;

use App\Modules\Rmsapi\src\Jobs\ExtractSkuAndProductIdJob;
use App\Models\Product;
use App\Models\RmsapiProductImport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExtractSkuProductIdJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_all_sku_are_populated()
    {
        // prepare
        factory(RmsapiProductImport::class, 5)->create([
            'sku' => null,
            'product_id' => null,
            'when_processed' => now(),
        ]);


        // do
        ExtractSkuAndProductIdJob::dispatchNow();


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
