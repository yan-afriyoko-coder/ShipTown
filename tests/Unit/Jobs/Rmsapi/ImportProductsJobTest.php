<?php

namespace Tests\Unit\Jobs\Rmsapi;

use App\Jobs\Rmsapi\ImportProductsJob;
use App\Models\RmsapiConnection;
use App\Models\RmsapiProductImport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportProductsJobTest extends TestCase
{
    /**
     *
     * @return void
     * @throws \Exception
     */
    public function test_batch_saving()
    {

        // cleanup data
        RmsapiConnection::query()->delete();
        RmsapiProductImport::query()->delete();

        // prepare defaults
        $rmsapiConnection = factory(RmsapiConnection::class)->create();
        $productCount = random_int(10,100);
        $products = factory(RmsapiProductImport::class, $productCount)->make();

        $productsCollection = collect($products)
            ->map(function ($product) {
                return $product->raw_import;
            })
            ->sortBy('db_change_stamp');

        // do the job
        $job = new ImportProductsJob($rmsapiConnection->id);
        $job->saveImportedProducts($productsCollection->toArray());

        // test if right data was saved
        $this->assertEquals(
            $productCount,
            RmsapiProductImport::query()
                ->where('connection_id', '=', $rmsapiConnection->id)
                ->where('batch_uuid', '=', $job->batch_uuid)
                ->count(),
            'Did not save all records'
        );

        $this->assertEquals(
            RmsapiConnection::find($rmsapiConnection->id)->products_last_timestamp,
            $productsCollection->last()['db_change_stamp'],
            'products_last_timestamp not updated correctly'
        );

    }
}
