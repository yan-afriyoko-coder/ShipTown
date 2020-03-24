<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        return Product::query()->paginate(10);
    }

    public function store(Request $request)
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

        $sns = new SnsTopicController("products");

        $sns->publish_message(json_encode($product->toArray()));

        $this->respond_OK_200();
    }
}
