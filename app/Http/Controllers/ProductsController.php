<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductsRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if($request->has('q') && $request->get('q')) {

            $product = ProductService::find($request->get('q'));

            if ($product) {
                $query->whereKey($product->getKey());
            } else {
                $query->where('sku', 'like', '%' . $request->get('q') . '%')
                    ->orWhere('name', 'like', '%' . $request->get('q') . '%');
            }

        }

        if($request->has('sort') ){
            $query->orderBy($request->get('sort'), $request->get('order', 'asc'));
        }

        $query->with('inventory');

        return $query->paginate(100);
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
