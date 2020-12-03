<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\Tags\Tag;

class ProductTagController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Tag::class);

        return TagResource::collection($this->getPaginatedResult($query));
    }
}
