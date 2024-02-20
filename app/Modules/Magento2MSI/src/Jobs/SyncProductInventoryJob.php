<?php

namespace App\Modules\Magento2MSI\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\Magento2MSI\src\Api\MagentoApi;
use App\Modules\Magento2MSI\src\Models\Magento2msiConnection;
use App\Modules\Magento2MSI\src\Models\Magento2msiProduct;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class SyncProductInventoryJob extends UniqueJob
{
    public function handle(): void
    {
        Magento2msiConnection::query()
            ->where('enabled', true)
            ->get()
            ->each(function (Magento2msiConnection $magentoConnection) {
                Magento2msiProduct::query()
                    ->with(['inventoryTotalByWarehouseTag', 'product'])
                    ->where('sync_required', true)
                    ->chunkById(50, function (Collection $chunk) use ($magentoConnection) {
                        usleep(100000); // Sleep for 0.1 seconds to avoid rate limiting

                        return $this->syncInventory($chunk, $magentoConnection);
                    });
            });
    }

    private function syncInventory(Collection $chunk, Magento2msiConnection $magentoConnection): bool
    {
        $sourceItems = $chunk->map(function (Magento2msiProduct $magentoProduct) use ($magentoConnection) {
            return [
                'sku' => $magentoProduct->sku,
                'source_code' => $magentoConnection->magento_source_code,
                'quantity' => $magentoProduct->inventoryTotalByWarehouseTag->quantity_available,
                'status' => 1,
            ];
        });

        MagentoApi::postInventorySourceItems($magentoConnection, $sourceItems);

        Magento2msiProduct::query()
            ->whereIn('id', $chunk->pluck('id'))
            ->update(['sync_required' => false, 'inventory_source_items_fetched_at' => null]);

        Log::info('Magento2msi - Inventory synced', [
            'connection_id' => $magentoConnection->getKey(),
            'count' => $sourceItems->count(),
        ]);

        return true;
    }
}
