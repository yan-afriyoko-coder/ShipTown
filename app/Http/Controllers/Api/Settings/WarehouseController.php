<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\StoreRequest;
use App\Http\Requests\Warehouse\UpdateRequest;
use App\Http\Resources\WarehouseResource;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Tags\Tag;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $query = Warehouse::getSpatieQueryBuilder();

        return WarehouseResource::collection($this->getPaginatedResult($query));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return WarehouseResource
     */
    public function store(StoreRequest $request)
    {
        $warehouse = new Warehouse;
        $warehouse->fill($request->validated());
        $warehouse->save();

        return new WarehouseResource($warehouse);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Warehouse $warehouse
     * @return WarehouseResource
     */
    public function update(UpdateRequest $request, Warehouse $warehouse): WarehouseResource
    {
        $warehouse->update($request->validated());

        $tags = data_get($request->validated(), 'tags', []);

        $tags = collect($tags)->filter()->map(function ($tag) use ($warehouse) {
            $warehouse->attachTag($tag);
            return Tag::findFromString($tag);
        });

        $warehouse->tags()->sync($tags->pluck('id'));

        return new WarehouseResource($warehouse);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Warehouse $warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return true;
    }
}
