<?php

namespace App\Modules\Magento2MSI\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\Magento2MSI\src\Api\MagentoApi;
use App\Modules\Magento2MSI\src\Models\Magento2msiConnection;
use App\Modules\Magento2MSI\src\Models\Magento2msiProduct;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AssignInventorySourceJob extends UniqueJob
{
    public function handle(): void
    {
        Magento2msiConnection::query()
            ->where('enabled', true)
            ->get()
            ->each(function (Magento2msiConnection $connection) {
                Magento2msiProduct::query()
                    ->where('connection_id', $connection->getKey())
                    ->whereNotNull('magento_product_id')
                    ->whereNull('source_assigned')
                    ->chunkById(50, function (Collection $products) use ($connection) {
                        try {
                            sleep(1); // Sleep for 1 second to avoid rate limiting

                            return $this->assignInventorySource($connection, $products);
                        } catch (Exception $exception) {
                            report($exception);
                            throw $exception;
                        }
                    });
            });
    }

    private function assignInventorySource(Magento2msiConnection $magento2msiConnection, Collection $products)
    {
        $response = MagentoApi::postInventoryBulkProductSourceAssign(
            $magento2msiConnection,
            $products->pluck('sku')->toArray(),
            [$magento2msiConnection->magento_source_code]
        );

        if ($response->failed()) {
            Log::error('Failed to fetch stock items', [
                'connection_id' => $magento2msiConnection->getKey(),
                'response' => $response->json(),
            ]);
            return false;
        }

        Magento2msiProduct::query()
            ->whereIn('id', $products->pluck('id'))
            ->update([
                'source_assigned' => 1,
                'inventory_source_items_fetched_at' => null,
            ]);

        Log::info('Fetched stock items', [
            'connection' => $magento2msiConnection->getKey(),
            'response' => $response->json('items'),
        ]);

        return true;
    }
}
