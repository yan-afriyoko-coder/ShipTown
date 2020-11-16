<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductAliasResource;
use App\Models\ProductAlias;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductAliasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = QueryBuilder::for(ProductAlias::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('product_id'),
                AllowedFilter::partial('alias'),
            ])
            ->allowedIncludes([
            ])
            ->allowedSorts([
            ]);

        return ProductAliasResource::collection($this->getPerPageAndPaginate($request, $query));
    }
}
