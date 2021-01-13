<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductKickController extends Controller
{
    public function index(Request $request, $sku)
    {
        $product = Product::query()->where(['sku' => $sku])->first();

        $product->attachTag('Not Synced');

        return redirect('products');
    }
}
