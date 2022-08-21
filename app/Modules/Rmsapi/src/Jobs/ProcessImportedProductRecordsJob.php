<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductAlias;
use App\Models\ProductPrice;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class ProcessImportedProductRecordsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        retry(5, function () {
            RmsapiProductImport::query()
                ->whereNull('when_processed')
                ->where('reserved_at', '<', now()->subMinutes(5))
                ->update(['reserved_at' => null]);
        });

         $reservationTime = now();

         RmsapiProductImport::query()
             ->whereNull('when_processed')
             ->whereNull('reserved_at')
             ->orderBy('id')
             ->limit(20)
             ->update(['reserved_at' => $reservationTime]);

        $records = RmsapiProductImport::query()
            ->whereNull('when_processed')
            ->where(['reserved_at' => $reservationTime])
            ->orderBy('id')
            ->get();

        $records->each(function (RmsapiProductImport $productImport) {
            retry(2, function () use ($productImport) {
                $this->import($productImport);
            });
        });

        if (RmsapiProductImport::query()->whereNull('when_processed')->exists()) {
            ProcessImportedProductRecordsJob::dispatch();
        }
    }


    /**
     * @param RmsapiProductImport $importedProduct
     */
    private function import(RmsapiProductImport $importedProduct): void
    {
        $attributes = [
            'sku'  => $importedProduct->raw_import['item_code'],
            'name' => $importedProduct->raw_import['description'],
        ];

        $product = Product::firstOrCreate(['sku' => $attributes['sku']], $attributes);

        $this->attachTags($importedProduct, $product);

        $this->importAliases($importedProduct, $product);

        $this->importInventory($importedProduct, $product);

        $this->importPricing($importedProduct, $product);

        $importedProduct->update([
            'when_processed' => now(),
            'product_id'     => $product->id,
            'sku'            => $attributes['sku'],
        ]);
    }

    /**
     * @param RmsapiProductImport $importedProduct
     * @param Product             $product
     */
    private function importAliases(RmsapiProductImport $importedProduct, Product $product): void
    {
        if (!Arr::has($importedProduct->raw_import, 'aliases')) {
            return;
        }

        foreach ($importedProduct->raw_import['aliases'] as $alias) {
            ProductAlias::query()->updateOrCreate([
                'alias'       => $alias['alias']
            ], [
                'product_id'  => $product->id
            ]);
        }
    }

    /**
     * @param RmsapiProductImport $importedProduct
     * @param Product             $product
     */
    private function importInventory(RmsapiProductImport $importedProduct, Product $product): void
    {
        $connection = RmsapiConnection::query()->find($importedProduct->connection_id);

        Inventory::query()
            ->where([
                'product_id'        => $product->id,
                'warehouse_code'    => $connection->location_id,
            ])
            ->first()
            ->update([
                'quantity'          => Arr::get($importedProduct->raw_import, 'quantity_on_hand', 0),
                'quantity_reserved' => Arr::get($importedProduct->raw_import, 'quantity_committed', 0),
                'quantity_incoming' => Arr::get($importedProduct->raw_import, 'quantity_on_order', 0),
                'shelve_location'   => Arr::get($importedProduct->raw_import, 'rmsmobile_shelve_location', ''),
                'reorder_point'     => Arr::get($importedProduct->raw_import, 'reorder_point', 0),
                'restock_level'     => Arr::get($importedProduct->raw_import, 'restock_level', 0),
            ]);
    }

    /**
     * @param RmsapiProductImport $importedProduct
     * @param Product             $product
     */
    private function importPricing(RmsapiProductImport $importedProduct, Product $product): void
    {
        $connection = RmsapiConnection::query()->find($importedProduct->connection_id);

        ProductPrice::query()
            ->where([
                'product_id'            => $product->id,
                'warehouse_code'        => $connection->location_id,
            ])
            ->first()
            ->update([
                'price'                 => $importedProduct->raw_import['price'],
                'sale_price'            => $importedProduct->raw_import['sale_price'],
                'sale_price_start_date' => $importedProduct->raw_import['sale_start_date'] ?? '1899-01-01 00:00:00',
                'sale_price_end_date'   => $importedProduct->raw_import['sale_end_date'] ?? '1899-01-01 00:00:00',
            ]);
    }

    /**
     * @param RmsapiProductImport $importedProduct
     * @param Product             $product
     */
    private function attachTags(RmsapiProductImport $importedProduct, Product $product): void
    {
        if ($importedProduct->raw_import['is_web_item']) {
            $product->attachTag('Available Online');
        } else {
            $product->detachTag('Available Online');
        }

        if ($importedProduct->raw_import['department_name']) {
            $product->attachTag($importedProduct->raw_import['department_name']);
        }

        if ($importedProduct->raw_import['category_name']) {
            $product->attachTag($importedProduct->raw_import['category_name']);
        }
    }
}
