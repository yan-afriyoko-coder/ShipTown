<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductAlias;
use Barryvdh\LaravelIdeHelper\Alias;
use Illuminate\Support\Facades\Log;

/**
 * Class ProductService.
 */
class ProductService
{
    public static function find(string $skuOrAlias)
    {
        $product = Product::findBySKU($skuOrAlias);

        if ($product) {
            return $product;
        }

        return self::findByAlias($skuOrAlias);
    }

    public static function findByAlias(string $alias)
    {
        $productAlias = ProductAlias::query()->where(['alias' => $alias])->with('product')->first();

        if ($productAlias) {
            return $productAlias->product()->first();
        }

        return null;
    }

    /**
     * @param string $sku
     * @param float  $quantity
     * @param string $message
     *
     * @return bool
     */
    public static function reserve(string $sku, float $quantity, string $message)
    {
        $aProduct = Product::query()->where(['sku' => $sku])->first();

        if ($aProduct) {
            $aProduct->increment('quantity_reserved', $quantity);

            return true;
        }

        Log::warning('Could not reserve quantity - SKU does not exist', ['sku' => $sku]);

        return false;
    }

    /**
     * @param string $sku
     * @param float  $quantity
     * @param string $message
     *
     * @return bool
     */
    public static function release(string $sku, float $quantity, string $message)
    {
        $aProduct = Product::query()->where(['sku' => $sku])->first();

        if ($aProduct) {
            $aProduct->decrement('quantity_reserved', $quantity);

            return true;
        }

        Log::warning('Could not release quantity - SKU does not exist', ['sku' => $sku]);

        return false;
    }

    public static function merge(string $sku1, string $sku2)
    {
        /** @var Product $product1 */
        $product1 = Product::findBySKU($sku1);

        /** @var Product $product2 */
        $product2 = Product::findBySKU($sku2);

        // Merge product2 into product
        $product2->update(['sku' => 'merged_to_'.$sku1.'_'.time()]);
        $product2->aliases()->update(['product_id' => $product1->id]);

        ProductAlias::query()->updateOrCreate(['alias' => $sku2], ['product_id' => $product1->id]);

        Inventory::query()
            ->where(['product_id' => $product2->id])
            ->where('quantity_available', '!=', 0)
            ->get()
            ->each(function (Inventory $p2_inventory) use ($product1, $product2) {
                $p1_inventory = Inventory::query()
                    ->where(['product_id' => $product1->id, 'warehouse_id' => $p2_inventory->warehouse_id])
                    ->first();

                if ($p2_inventory->quantity != 0) {
                    InventoryService::adjustQuantity(
                        $p1_inventory,
                        $p2_inventory->quantity,
                        'merging from "'.$product2->sku.'"'
                    );

                    InventoryService::adjustQuantity(
                        $p2_inventory,
                        -$p2_inventory->quantity,
                        'merging to "'.$product1->sku.'"'
                    );
                }

                $p2_inventory->update(['quantity_reserved' => 0]);
            });
    }
}
