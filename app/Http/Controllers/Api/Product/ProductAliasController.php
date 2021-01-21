<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductAliasResource;
use App\Models\ProductAlias;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class ProductAliasController
 * @package App\Http\Controllers\Api\Product
 */
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
        $query = ProductAlias::getSpatieQueryBuilder();

        return ProductAliasResource::collection($this->getPaginatedResult($query));
    }
}
