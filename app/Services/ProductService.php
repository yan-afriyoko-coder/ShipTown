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

    public static function merge(Product $product1, Product $product2)
    {
        $sku = $product2->sku;

        // Merge product2 into product
        $product2->update(['sku' => 'merged_to_'.$product1->sku.'_'.time()]);
        $product2->aliases()->update(['product_id' => $product1->id]);

        ProductAlias::query()->updateOrCreate([
            'alias' => $sku,
        ], [
            'product_id' => $product1->id,
        ]);

        Inventory::query()
            ->where(['product_id' => $product2->id])
            ->where('quantity', '!=', 0)
            ->get()
            ->each(function (Inventory $p2_inventory) use ($product1) {
                $p1_inventory = Inventory::query()
                    ->where(['product_id' => $product1->id, 'warehouse_id' => $p2_inventory->warehouse_id])
                    ->first();

                InventoryService::adjustQuantity($p1_inventory, $p2_inventory->quantity, $p2_inventory->quantity);
                InventoryService::adjustQuantity($p2_inventory, -$p2_inventory->quantity, $p2_inventory->quantity);
            });
    }
}
