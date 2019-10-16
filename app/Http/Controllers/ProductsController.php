<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function store(Request $request)
    {
        $product = Product::updateOrCreate(
            ['sku' => $request->sku],
            $request->all()
        );

        return response()->json($product, 200);
    }
}
