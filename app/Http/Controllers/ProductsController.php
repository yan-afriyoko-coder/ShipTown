<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductsRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->when($request->has('q'), function ($query) use ($request) {
                return $query
                    ->where('sku', 'like', '%' . $request->get('q') . '%')
                    ->orWhere('name', 'like', '%' . $request->get('q') . '%');
            })
            ->when($request->has('sort'), function ($query) use ($request) {
                return $query
                    ->orderBy($request->get('sort'), $request->get('order', 'asc'));
            });
            
        return $products->paginate(100);
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

        $this->respond_OK_200();
    }
}
