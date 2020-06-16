<?php

namespace Tests\Unit\Jobs\Api2cart;

use App\Jobs\Api2cart\ImportProductsJob;
use App\Models\Api2cartOrderImports;
use App\Models\Product;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class ImportProductsJobTest
 * @package Tests\Unit\Jobs\Api2cart
 */
class ImportProductsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_if_processes_correctly() {
        // Test for clean data
        $orderImport = factory(Api2cartOrderImports::class)->create();

        $this->assertEquals(0, Product::whereIn('name', ['Bag', 'Box'])->count());
        $job = new ImportProductsJob($orderImport);
        $job->handle();

        // Test if the products from the factory were added.
        $this->assertEquals(2, Product::whereIn('name', ['Bag', 'Box'])->count());
    }
}
