<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductAliasResource;
use App\Models\ProductAlias;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class ProductAliasController.
 */
class ProductAliasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $query = ProductAlias::getSpatieQueryBuilder();

        return ProductAliasResource::collection($this->getPaginatedResult($query));
    }
}
