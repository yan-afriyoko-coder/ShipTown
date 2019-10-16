<?php


namespace App\Managers;

use App\Models\Product;

class ProductManager
{
    public static function reserve($sku, $quantity, $message)
    {
        $aProduct = Product::firstOrCreate(["sku" => $sku]);

        $aProduct->increment("quantity_reserved", $quantity);
    }

    public static function release($sku, $quantity, $message)
    {
        $aProduct = Product::firstOrCreate(["sku" => $sku]);

        $aProduct->decrement("quantity_reserved", $quantity);
    }

}
