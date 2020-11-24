<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductsRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Product::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('sku'),
                AllowedFilter::exact('name'),
                AllowedFilter::exact('price'),

                AllowedFilter::scope('q', 'whereHasText'), // to be removed, left for backwards compatibility only text should be used
                AllowedFilter::scope('search', 'whereHasText'),
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

        return $this->getPaginatedResult($query, 100);
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
