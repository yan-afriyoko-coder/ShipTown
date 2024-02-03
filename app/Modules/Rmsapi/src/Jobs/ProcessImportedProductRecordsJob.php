<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductAlias;
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

        $this->createNewProducts();

        $this->fillProductIds();

        $this->fillInventoryIds();

        $this->fillProductPricesIds();

        do {
            $this->processImportedProducts($batch_size);

            $hasRecordsToProcess = RmsapiProductImport::query()
                ->whereNotNull('inventory_id')
                ->whereNull('processed_at')
                ->exists();

            usleep(100000); // 0.1 sec
        } while ($hasRecordsToProcess);

        return true;
    }

    private function processImportedProducts(int $batch_size): void
    {
        $reservationTime = now();

        $updatedRecords = RmsapiProductImport::query()
            ->whereNotNull('inventory_id')
            ->whereNull('processed_at')
            ->orderBy('id')
            ->limit($batch_size)
            ->update(['reserved_at' => $reservationTime]);

        Log::debug('Job processing', [
            'job' => self::class,
            'updatedRecords' => $updatedRecords,
            'reservationTime' => $reservationTime,
        ]);

        ray('RmsapiProductImport', RmsapiProductImport::all()->toArray());
        $records = RmsapiProductImport::query()->with(['product', 'inventory', 'prices'])
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
        $importedProduct->product->update([
            'sku' => $importedProduct->sku,
            'name' => $importedProduct->name,
            'department' => data_get($importedProduct, 'department_name', ''),
            'category' => data_get($importedProduct, 'category_name', ''),
        ]);

        $this->attachTags($importedProduct);

        $this->importAliases($importedProduct);

        $this->importInventory($importedProduct);

        $this->importPricing($importedProduct);

        $importedProduct->update([
            'processed_at' => now(),
        ]);
    }

    private function importAliases(RmsapiProductImport $importedProduct): void
    {
        if (!Arr::has($importedProduct->raw_import, 'aliases')) {
            return;
        }

        foreach ($importedProduct->raw_import['aliases'] as $alias) {
            $productAlias = ProductAlias::query()
                ->firstOrCreate([
                    'alias'      => $alias['alias'],
                ], [
                    'product_id' => $importedProduct->product->id,
                ]);

            if ($productAlias->product_id !== $importedProduct->product->id) {
                $productAlias->update(['product_id' => $importedProduct->product->id]);
            }
        }
    }

    private function importInventory(RmsapiProductImport $importedProduct): void
    {
        $importedProduct->inventory->update([
            'quantity_reserved' => data_get($importedProduct->raw_import, 'quantity_committed', 0),
            'reorder_point'     => data_get($importedProduct->raw_import, 'reorder_point', 0),
            'restock_level'     => data_get($importedProduct->raw_import, 'restock_level', 0),
        ]);
    }

    private function importPricing(RmsapiProductImport $importedProduct): void
    {
        $importedProduct->prices->update([
            'price'                 => $importedProduct->raw_import[$importedProduct->rmsapiConnection->price_field_name],
            'cost'                  => $importedProduct->raw_import['cost'],
            'sale_price'            => $importedProduct->raw_import['sale_price'],
            'sale_price_start_date' => $importedProduct->raw_import['sale_start_date'] ?? '2000-01-01',
            'sale_price_end_date'   => $importedProduct->raw_import['sale_end_date'] ?? '2000-01-01',
        ]);
    }

    private function attachTags(RmsapiProductImport $importedProduct): void
    {
        if ($importedProduct->raw_import['is_web_item']) {
            $importedProduct->product->attachTag('Available Online');
        } else {
            $importedProduct->product->detachTag('Available Online');
        }

        if ($importedProduct->raw_import['department_name']) {
            $importedProduct->product->syncTagByType('rms_department_name', trim($importedProduct->raw_import['department_name']));
        }

        if ($importedProduct->raw_import['category_name']) {
            $importedProduct->product->syncTagByType('rms_category_name', trim($importedProduct->raw_import['category_name']));
        }

        if ($importedProduct->raw_import['supplier_name']) {
            $importedProduct->product->syncTagByType('rms_supplier_name', trim($importedProduct->raw_import['supplier_name']));
        }

        if ($importedProduct->raw_import['sub_description_1']) {
            $importedProduct->product->syncTagByType('rms_sub_description_1', trim($importedProduct->raw_import['sub_description_1']));
        }

        if ($importedProduct->raw_import['sub_description_2']) {
            $importedProduct->product->syncTagByType('rms_sub_description_2', trim($importedProduct->raw_import['sub_description_2']));
        }

        if ($importedProduct->raw_import['sub_description_3']) {
            $importedProduct->product->syncTagByType('rms_sub_description_3', trim($importedProduct->raw_import['sub_description_3']));
        }
    }

    protected function fillProductIds(): void
    {
        do {
            $recordsUpdated = DB::affectingStatement('
                WITH fillProductIds AS (
                    SELECT modules_rmsapi_products_imports.id as record_id, products_aliases.product_id

                    FROM modules_rmsapi_products_imports
                    INNER JOIN products_aliases
                        ON modules_rmsapi_products_imports.sku = products_aliases.alias

                    WHERE modules_rmsapi_products_imports.product_id IS NULL

                    LIMIT 200
                )

                UPDATE modules_rmsapi_products_imports

                INNER JOIN fillProductIds
                    ON modules_rmsapi_products_imports.id = fillProductIds.record_id

                SET modules_rmsapi_products_imports.product_id = fillProductIds.product_id
            ');

            usleep(100000); // 100ms
        } while ($recordsUpdated > 0);
    }

    protected function fillInventoryIds(): void
    {
        ray('inventory', Inventory::all()->toArray())->expand(2);

        do {
            $recordsUpdated = DB::affectingStatement('
                WITH fillInventoryIds AS (
                    SELECT modules_rmsapi_products_imports.id as record_id, inventory.id as inventory_id

                    FROM modules_rmsapi_products_imports
                    INNER JOIN inventory
                        ON modules_rmsapi_products_imports.product_id = inventory.product_id
                        AND modules_rmsapi_products_imports.warehouse_id = inventory.warehouse_id

                    WHERE modules_rmsapi_products_imports.inventory_id IS NULL
                    AND modules_rmsapi_products_imports.product_id IS NOT NULL
                    AND modules_rmsapi_products_imports.warehouse_id IS NOT NULL

                    LIMIT 200
                )
                UPDATE modules_rmsapi_products_imports
                INNER JOIN fillInventoryIds
                    ON modules_rmsapi_products_imports.id = fillInventoryIds.record_id

                SET modules_rmsapi_products_imports.inventory_id = fillInventoryIds.inventory_id
            ');

            usleep(100000); // 100ms
        } while ($recordsUpdated > 0);
    }

    private function fillProductPricesIds()
    {
        do {
            $recordsUpdated = DB::affectingStatement('
                WITH fillProductPricesIds AS (
                    SELECT modules_rmsapi_products_imports.id as record_id, products_prices.id as product_price_id

                    FROM modules_rmsapi_products_imports
                    INNER JOIN products_prices
                        ON modules_rmsapi_products_imports.product_id = products_prices.product_id
                        AND modules_rmsapi_products_imports.warehouse_id = products_prices.warehouse_id

                    WHERE modules_rmsapi_products_imports.product_price_id IS NULL
                        AND modules_rmsapi_products_imports.product_id IS NOT NULL
                        AND modules_rmsapi_products_imports.warehouse_id IS NOT NULL

                    LIMIT 200
                )

                UPDATE modules_rmsapi_products_imports
                INNER JOIN fillProductPricesIds
                    ON modules_rmsapi_products_imports.id = fillProductPricesIds.record_id

                SET modules_rmsapi_products_imports.product_price_id = fillProductPricesIds.product_price_id
            ');

            usleep(100000); // 100ms
        } while ($recordsUpdated > 0);
    }

    private function createNewProducts(): void
    {
        do {
            $recordsInserted = DB::affectingStatement('
                INSERT INTO products (sku, name, created_at, updated_at)
                SELECT
                    modules_rmsapi_products_imports.sku,
                    modules_rmsapi_products_imports.name,
                    NOW() as created_at,
                    NOW() as updated_at
                FROM modules_rmsapi_products_imports

                LEFT JOIN products
                    ON products.sku = modules_rmsapi_products_imports.sku

                WHERE
                    modules_rmsapi_products_imports.product_id IS NULL
                    AND products.id IS NOT NULL

                LIMIT 200;
            ');

            usleep(100000); // 100ms
        } while ($recordsInserted > 0);
    }
}
