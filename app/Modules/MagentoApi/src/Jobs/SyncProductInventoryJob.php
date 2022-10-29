<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Models\Product;
use App\Modules\MagentoApi\src\Api\StockItems;
use App\Modules\MagentoApi\src\Models\MagentoProductInventoryComparisonView;
use Grayloon\Magento\Magento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\Response;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class SyncCheckFailedProductsJob.
 */
class SyncProductInventoryJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var Product
     */
    private Product $product;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        MagentoProductInventoryComparisonView::query()
            ->whereRaw('magento_quantity != expected_quantity')
            ->get()
            ->each(function (MagentoProductInventoryComparisonView $comparison) {
                $this->syncProductInventory($comparison);

                $comparison->magentoProduct->update([
                    'stock_items_raw_import' => null,
                ]);
            });
    }

    /**
     * @return Response|void
     */
    private function syncProductInventory(MagentoProductInventoryComparisonView $comparison)
    {
        $product = $comparison->magentoProduct->product;

        $stockItems = new StockItems(new Magento());

        $params = [
            'is_in_stock' => $product->quantity_available > 0,
            'qty' => $product->quantity_available,
        ];

        $response = $stockItems->update($product->sku, $params);

        Log::debug('MagentoApi: stockItem update', [
            'sku'                  => $product->sku,
            'response_status_code' => $response->status(),
            'response_body'        => $response->json(),
            'params'               => $params
        ]);

        return $response;
    }
}
