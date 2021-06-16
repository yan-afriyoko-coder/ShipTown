<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\Tags\Tag;

/**
 * Class ProductTagController
 * @package App\Http\Controllers\Api\Product
 */
class ProductTagController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $query = QueryBuilder::for(Tag::class);

        return TagResource::collection($this->getPaginatedResult($query));
    }
}
