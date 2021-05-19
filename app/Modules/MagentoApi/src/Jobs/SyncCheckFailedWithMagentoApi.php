<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Models\Product;
use App\Modules\MagentoApi\src\Api\StockItems;
use Grayloon\Magento\Magento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class SyncCheckFailedWithMagentoApi
 * @package App\Jobs
 */
class SyncCheckFailedWithMagentoApi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Product|null
     */
    private ?Product $product;
    private StockItems $stockItems;
    private Magento $magento;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Product $product = null)
    {
        $this->product = $product;
        $this->magento = new Magento();
        $this->stockItems = new StockItems($this->magento);
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('Starting ' . self::class);
        $log_context = [];

        $productsCollection = collect();

        if ($this->product) {
            $productsCollection->add($this->product);
            $log_context['sku'] = $this->product->sku;
        } else {
            $productsCollection = Product::withAllTags(['CHECK FAILED'])
                ->limit(50)
                ->get();
            $log_context['products_selected_count'] = $productsCollection->count();
        }

        $log_context['products_synced_count'] = 0;

        $productsCollection->each(function (Product $product) use ($log_context) {
                $params = [
                    'is_in_stock' => $product->quantity_available > 0,
                    'qty' => $product->quantity_available,
                ];
                $response = $this->stockItems->update($product->sku, $params);

                if ($response->ok()) {
                    $product->log('Stock synced with Magento API')
                        ->detachTag('CHECK FAILED');
                    $log_context['products_synced_count']++;
                }

                Log::debug('MagentoApi: stockItem updated', [
                    'sku' => $product->sku,
                    'params' => $params,
                    'response_status_code' => $response->status(),
                    'response_body' => $response->json(),
                ]);
        });

        Log::info('Finished '. self::class, $log_context);
    }
}
