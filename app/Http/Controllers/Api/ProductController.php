<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductsRequest;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class ProductController
 * @package App\Http\Controllers\Api
 *
 * @group Products
 */
class ProductController extends Controller
{
    /**
     * GET
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $query = Product::getSpatieQueryBuilder();

        return $this->getPaginatedResult($query);
    }

    /**
     * POST
     *
     * @param StoreProductsRequest $request
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
        $product = Product::query()->where("sku", $sku)->firstOrFail();

        $product->save();

        $this->respondOK200();
    }
}
