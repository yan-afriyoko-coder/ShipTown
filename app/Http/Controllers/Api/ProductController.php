<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductsRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class ProductController.
 *
 * @group Products
 */
class ProductController extends Controller
{
    /**
     * Get Product List.
     *
     * @param Request $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = Product::getSpatieQueryBuilder();

        return ProductResource::collection($this->getPaginatedResult($query));
    }

    /**
     * Update or Create Product.
     *
     * @param StoreProductsRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreProductsRequest $request)
    {
        $product = Product::query()->updateOrCreate(
            ['sku' => $request->sku],
            $request->all()
        );

        return response()->json($product, 200);
    }

    /**
     * @param $sku
     */
    public function publish($sku)
    {
        $product = Product::query()->where('sku', $sku)->firstOrFail();

        $product->save();

        $this->respondOK200();
    }
}
