<?php

namespace Tests\Unit\Modules\MagentoApi;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Warehouse;
use App\Modules\MagentoApi\src\EventServiceProviderBase;
use App\Modules\MagentoApi\src\Jobs\CheckIfSyncIsRequiredJob;
use App\Modules\MagentoApi\src\Jobs\EnsureProductPriceIdIsFilledJob;
use App\Modules\MagentoApi\src\Jobs\EnsureProductRecordsExistJob;
use App\Modules\MagentoApi\src\Jobs\FetchBasePricesJob;
use App\Modules\MagentoApi\src\Jobs\FetchSpecialPricesJob;
use App\Modules\MagentoApi\src\Jobs\SyncProductBasePricesJob;
use App\Modules\MagentoApi\src\Jobs\SyncProductSalePricesJob;
use App\Modules\MagentoApi\src\Models\MagentoConnection;
use App\Modules\MagentoApi\src\Models\MagentoProduct;
use Tests\TestCase;

class BasicWorkflowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        EventServiceProviderBase::enableModule();
    }
    /** @test */
    public function test_module_basic_functionality()
    {
        if (empty(env('TEST_MODULES_MAGENTO2MSI_BASE_URL'))) {
            $this->markTestSkipped('Magento base url not set');
        }

        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create(['sku' => '45']);

        ProductPrice::query()->update([
            'price' => rand(1, 1000),
            'sale_price' => rand(1, 1000),
            'sale_price_start_date' => now()->subDays(rand(1, 10)),
            'sale_price_end_date' => now()->addDays(rand(1, 10)),
        ]);

        $magentoConnection = MagentoConnection::query()->create([
            'base_url' => env('TEST_MODULES_MAGENTO2MSI_BASE_URL'),
            'pricing_source_warehouse_id' => $warehouse->id,
        ]);

        $magentoConnection->api_access_token = env('TEST_MODULES_MAGENTO2MSI_ACCESS_TOKEN');
        $magentoConnection->save();

        $magentoProduct = MagentoProduct::query()->create([
            'connection_id' => $magentoConnection->id,
            'product_id' => $product->id,
            'base_prices_fetched_at' => null,
            'base_prices_raw_import' => null,
            'exists_in_magento' => 1
        ]);

        EnsureProductRecordsExistJob::dispatch();
        $this->assertDatabaseHas('modules_magento2api_products', ['product_id' => $product->id]);

        EnsureProductPriceIdIsFilledJob::dispatch();
        $this->assertDatabaseMissing('modules_magento2api_products', ['product_price_id' => null]);

        FetchBasePricesJob::dispatch();
        $this->assertDatabaseMissing('modules_magento2api_products', ['base_prices_raw_import' => null]);
        $this->assertDatabaseMissing('modules_magento2api_products', ['magento_price' => null]);

        FetchSpecialPricesJob::dispatch();
        $this->assertDatabaseMissing('modules_magento2api_products', ['special_prices_fetched_at' => null]);
        $this->assertDatabaseMissing('modules_magento2api_products', ['special_prices_raw_import' => null]);

        CheckIfSyncIsRequiredJob::dispatch();
        $this->assertDatabaseHas('modules_magento2api_products', ['base_price_sync_required' => true]);
        $this->assertDatabaseHas('modules_magento2api_products', ['special_price_sync_required' => true]);

        SyncProductBasePricesJob::dispatch();
        $this->assertDatabaseHas('modules_magento2api_products', ['base_prices_raw_import' => null]);
        $this->assertDatabaseHas('modules_magento2api_products', ['base_price_sync_required' => null]);

        SyncProductSalePricesJob::dispatch();
        $this->assertDatabaseHas('modules_magento2api_products', ['special_price_sync_required' => null]);
        $this->assertDatabaseHas('modules_magento2api_products', ['special_prices_fetched_at' => null]);

        FetchBasePricesJob::dispatch();
        $this->assertDatabaseMissing('modules_magento2api_products', ['base_prices_raw_import' => null]);
        $this->assertDatabaseMissing('modules_magento2api_products', ['magento_price' => null]);

        FetchSpecialPricesJob::dispatch();
        $this->assertDatabaseMissing('modules_magento2api_products', ['special_prices_fetched_at' => null]);
        $this->assertDatabaseMissing('modules_magento2api_products', ['special_prices_raw_import' => null]);

        CheckIfSyncIsRequiredJob::dispatch();
        $this->assertDatabaseHas('modules_magento2api_products', ['base_price_sync_required' => false]);
        $this->assertDatabaseHas('modules_magento2api_products', ['special_price_sync_required' => false]);
    }
}
