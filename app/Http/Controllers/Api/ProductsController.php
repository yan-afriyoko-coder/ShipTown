<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductsRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Product::class)
            ->allowedFilters([
                'id',
                'sku',
                'name',
                'price',
                AllowedFilter::scope('sku_or_alias'),
                AllowedFilter::scope('inventory_source_location_id', 'addInventorySource')->default(100),
            ])
            ->allowedSorts([
                'id',
                'sku',
                'name',
                'price',
                'quantity'
            ])
            ->allowedIncludes([
                'inventory',
                'aliases'
            ]);

        if ($request->has('q') && $request->get('q')) {
            $query->where('sku', 'like', '%' . $request->get('q') . '%')
                ->orWhere('name', 'like', '%' . $request->get('q') . '%');
        }

        return $query->paginate(100)->appends($request->query());
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
