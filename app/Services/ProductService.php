<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductAlias;
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
}
