<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductsRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MissingController extends Controller
{
    public function index(Request $request)
    {
        return Product::query()->paginate(10);
    }

    public function store(StoreProductsRequest $request)
    {
        Log::debug('Received product update request', [
            'request'=>$request->all()
        ]);

        $product = Product::query()->updateOrCreate(
            ['sku' => $request->sku],
            $request->all()
        );

        return response()->json($product, 200);
    }

    public function publish($sku)
    {
        $product = Product::query()->where("sku", $sku)->firstOrFail();

        $product->save();

        $this->respondOK200();
    }
}
