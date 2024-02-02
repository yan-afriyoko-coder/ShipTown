<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductAlias;
use App\Models\ProductPrice;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessImportedProductRecordsJob extends UniqueJob
{
    public function handle(): bool
    {
        $batch_size = 200;

        $this->fillProductIds();

        $this->fillInventoryIds();

        do {
            $this->processImportedProducts($batch_size);

            $hasRecordsToProcess = RmsapiProductImport::query()
                ->whereNull('reserved_at')
                ->whereNull('processed_at')
                ->exists();

            usleep(200000); // 0.1 sec
        } while ($hasRecordsToProcess);

        return true;
    }

    private function processImportedProducts(int $batch_size): void
    {
        $reservationTime = now();

        $updatedRecords = RmsapiProductImport::query()
            ->whereNotNull('inventory_id')
            ->whereNull('reserved_at')
            ->whereNull('processed_at')
            ->orderBy('id')
            ->limit($batch_size)
            ->update(['reserved_at' => $reservationTime]);

        Log::debug('Job processing', [
            'job' => self::class,
            'updatedRecords' => $updatedRecords,
            'reservationTime' => $reservationTime,
        ]);

        $records = RmsapiProductImport::query()
            ->where(['reserved_at' => $reservationTime])
            ->whereNull('processed_at')
            ->orderBy('id')
            ->get();

        $records->each(function (RmsapiProductImport $productImport) {
            try {
                $this->import($productImport);
            } catch (Exception $e) {
                report($e);
                Log::emergency($e->getMessage(), $e->getTrace());
            }
        });
    }

    private function import(RmsapiProductImport $importedProduct): void
    {
        $product = null;

        if ($importedProduct->product_id) {
            $product = Product::findOrFail($importedProduct->product_id);
        }

        if ($product === null) {
            $product = Product::query()->firstOrCreate(['sku' => $importedProduct->sku], [
                'sku'  => $importedProduct->sku,
                'name' => $importedProduct->name,
                'department' => $importedProduct->raw_import['department_name'],
                'category' => $importedProduct->raw_import['category'],
            ]);
        }

        if ($product->sku !== $importedProduct->sku) {
            Log::debug('ProcessImportedProductRecordsJob updating product sku', [
                'sku' => $product->sku,
                'old_sku' => $product->sku,
                'new_sku' => $importedProduct->sku,
            ]);
            $product->update(['sku' => $importedProduct->sku]);
        }

        if ($product->name !== $importedProduct->name) {
            Log::debug('ProcessImportedProductRecordsJob updating product name', [
                'sku' => $product->sku,
                'old_name' => $product->name,
                'new_name' => $importedProduct->name,
            ]);
            $product->update(['name' => $importedProduct->name]);
        }

        $this->attachTags($importedProduct, $product);

        $this->importAliases($importedProduct, $product);

        $this->importInventory($importedProduct, $product);

        $this->importPricing($importedProduct, $product);

        $importedProduct->update([
            'processed_at' => now(),
            'product_id'     => $product->id,
            'sku'            => $importedProduct->sku,
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
            $productAlias = ProductAlias::query()
                ->firstOrCreate([
                    'alias'      => $alias['alias'],
                ], [
                    'product_id' => $product->id,
                ]);

            if ($productAlias->product_id !== $product->id) {
                $productAlias->update(['product_id' => $product->id]);
            }
        }
    }

    /**
     * @param RmsapiProductImport $ip
     * @param Product             $product
     */
    private function importInventory(RmsapiProductImport $ip, Product $product): void
    {
        $connection = RmsapiConnection::query()->find($ip->connection_id);

        $inventory = Inventory::query()
            ->where([
                'product_id' => $product->id,
                'warehouse_id' => $connection->warehouse_id,
            ])
            ->first();

        if ($inventory->quantity_reserved !== Arr::get($ip->raw_import, 'quantity_committed', 0)
            or $inventory->reorder_point !== Arr::get($ip->raw_import, 'reorder_point', 0)
            or $inventory->restock_level !== Arr::get($ip->raw_import, 'restock_level', 0)
        ) {
            $inventory->update([
                'quantity_reserved' => Arr::get($ip->raw_import, 'quantity_committed', 0),
                'reorder_point'     => Arr::get($ip->raw_import, 'reorder_point', 0),
                'restock_level'     => Arr::get($ip->raw_import, 'restock_level', 0),
            ]);
        }
    }

    /**
     * @param RmsapiProductImport $importedProduct
     * @param Product             $productPrice
     */
    private function importPricing(RmsapiProductImport $importedProduct, Product $productPrice): void
    {
        $connection = RmsapiConnection::query()->find($importedProduct->connection_id);

        $productPrice = ProductPrice::query()
            ->where([
                'product_id' => $productPrice->id,
                'warehouse_id' => $connection->warehouse_id,
            ])
            ->first();

        // price, price_a, price_b, price_c
        $priceFieldName = $importedProduct->rmsapiConnection->price_field_name;

        if ($productPrice->price !== $importedProduct->raw_import[$priceFieldName]
            or $productPrice->cost !== $importedProduct->raw_import['cost']
            or $productPrice->sale_price !== $importedProduct->raw_import['sale_price']
            or $productPrice->sale_price_start_date !== $importedProduct->raw_import['sale_price_start_date'] ?? '2000-01-01'
            or $productPrice->sale_price_end_date !== $importedProduct->raw_import['sale_price_end_date'] ?? '2000-01-01'
        ) {
            $productPrice->update([
                'price'                 => $importedProduct->raw_import[$priceFieldName],
                'cost'                  => $importedProduct->raw_import['cost'],
                'sale_price'            => $importedProduct->raw_import['sale_price'],
                'sale_price_start_date' => $importedProduct->raw_import['sale_start_date'] ?? '2000-01-01',
                'sale_price_end_date'   => $importedProduct->raw_import['sale_end_date'] ?? '2000-01-01',
            ]);
        }
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
            $product->syncTagByType('rms_department_name', trim($importedProduct->raw_import['department_name']));
        }

        if ($importedProduct->raw_import['category_name']) {
            $product->syncTagByType('rms_category_name', trim($importedProduct->raw_import['category_name']));
        }

        if ($importedProduct->raw_import['supplier_name']) {
            $product->syncTagByType('rms_supplier_name', trim($importedProduct->raw_import['supplier_name']));
        }

        if ($importedProduct->raw_import['sub_description_1']) {
            $product->syncTagByType('rms_sub_description_1', trim($importedProduct->raw_import['sub_description_1']));
        }

        if ($importedProduct->raw_import['sub_description_2']) {
            $product->syncTagByType('rms_sub_description_2', trim($importedProduct->raw_import['sub_description_2']));
        }

        if ($importedProduct->raw_import['sub_description_3']) {
            $product->syncTagByType('rms_sub_description_3', trim($importedProduct->raw_import['sub_description_3']));
        }
    }

    protected function fillProductIds(): void
    {
        do {
            $recordsUpdated = DB::update('
                UPDATE modules_rmsapi_products_imports
                INNER JOIN products_aliases
                    ON modules_rmsapi_products_imports.sku = products_aliases.alias

                SET modules_rmsapi_products_imports.product_id = products_aliases.product_id

                WHERE modules_rmsapi_products_imports.product_id IS NULL

                LIMIT 200;
            ');

            usleep(100000); // 100ms
        } while ($recordsUpdated > 0);
    }

    protected function fillInventoryIds(): void
    {
        do {
            $recordsUpdated = DB::update('
                UPDATE modules_rmsapi_products_imports
                INNER JOIN inventory
                    ON modules_rmsapi_products_imports.product_id = inventory.product_id
                    AND modules_rmsapi_products_imports.warehouse_id = inventory.warehouse_id

                SET modules_rmsapi_products_imports.inventory_id = inventory.id

                WHERE modules_rmsapi_products_imports.inventory_id IS NULL
                AND modules_rmsapi_products_imports.product_id IS NOT NULL
                AND modules_rmsapi_products_imports.warehouse_id IS NOT NULL

                LIMIT 200;
            ');

            usleep(100000); // 100ms
        } while ($recordsUpdated > 0);
    }
}
