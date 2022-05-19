<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProductInventoryController.
 */
class ProductInventoryController extends Controller
{
    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $query = Inventory::getSpatieQueryBuilder();

        return InventoryResource::collection($this->getPaginatedResult($query));
    }

    /**
     * @param StoreInventoryRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function store(StoreInventoryRequest $request): AnonymousResourceCollection
    {
        $product = Product::where('sku', '=', $request->sku)->firstOrFail();

        $attributes = $request->validated();

        $attributes['product_id'] = $product->id;

        $inventory = Inventory::query()->where([
                'product_id'     => $attributes['product_id'],
                'warehouse_code' => $attributes['location_id'],
            ])
            ->first();

        $inventory->update($attributes);

        return JsonResource::collection(collect([$inventory]));
    }
}
