<?php

namespace Tests\Unit\Jobs\Rmsapi;

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
        factory(RmsapiProductImport::class, 10)->create([
            'sku' => null,
            'product_id' => null,
            'when_processed' => now(),
        ]);

        $productImports = RmsapiProductImport::query()
            ->whereNotNull('when_processed')
            ->whereNull('sku')
            ->get();

        foreach ($productImports as $importedProduct ) {

            $importedProduct['sku'] = $importedProduct['raw_import']['item_code'];
            $importedProduct['product_id'] = Product::query()
                ->where('sku','=', $importedProduct['raw_import']['item_code'])
                ->first()->getKey();

            $importedProduct->save();
        }

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
