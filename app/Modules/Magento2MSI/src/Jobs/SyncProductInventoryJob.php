<?php

namespace App\Modules\Magento2MSI\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\Magento2MSI\src\Api\MagentoApi;
use App\Modules\Magento2MSI\src\Models\Magento2msiConnection;
use App\Modules\Magento2MSI\src\Models\Magento2msiProduct;
use Illuminate\Support\Collection;

class SyncProductInventoryJob extends UniqueJob
{
    public function handle(): void
    {
        Magento2msiConnection::query()
            ->get()
            ->each(function (Magento2msiConnection $magentoConnection) {
                Magento2msiProduct::query()
                    ->with(['inventoryTotalByWarehouseTag', 'product'])
                    ->where('sync_required', true)
                    ->chunkById(10, function (Collection $chunk) use ($magentoConnection) {
                        $sourceItems = $chunk->map(function (Magento2msiProduct $magentoProduct) use ($magentoConnection) {
                            return [
                                'sku' => $magentoProduct->product->sku,
                                'source_code' => $magentoConnection->magento_source_code,
                                'quantity' => $magentoProduct->inventoryTotalByWarehouseTag->quantity_available,
                                'status' => 1,
                            ];
                        });

                        MagentoApi::postInventorySourceItems($magentoConnection, $sourceItems);

                        Magento2msiProduct::query()
                            ->whereIn('id', $chunk->pluck('id'))
                            ->update(['sync_required' => false, 'inventory_source_items_fetched_at' => null]);
                    });
            });
    }
}
