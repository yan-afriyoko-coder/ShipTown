<?php

namespace App\Http\Controllers;

use App\Jobs\Modules\Api2cart\SyncProductJobCopy;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductKickController extends Controller
{
    public function index(Request $request, $sku)
    {
        $product = Product::query()->where(['sku' => $sku])->first();

        SyncProductJobCopy::dispatch($product);

        return redirect('products');
    }
}
