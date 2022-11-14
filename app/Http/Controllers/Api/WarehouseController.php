<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\StoreRequest;
use App\Http\Requests\Warehouse\UpdateRequest;
use App\Http\Requests\WarehouseIndexRequest;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Tags\Tag;

class WarehouseController extends Controller
{
    public function index(WarehouseIndexRequest $request): AnonymousResourceCollection
    {
        $query = Warehouse::getSpatieQueryBuilder()
            ->defaultSort('name');

        return WarehouseResource::collection($this->getPaginatedResult($query, 999));
    }

    public function store(StoreRequest $request): WarehouseResource
    {
        $warehouse = new Warehouse;
        $warehouse->fill($request->validated());
        $warehouse->save();

        return new WarehouseResource($warehouse);
    }

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
     * @return WarehouseResource
     * @throws Exception
     */
    public function destroy(Warehouse $warehouse): WarehouseResource
    {
        $warehouse->delete();

        return WarehouseResource::make($warehouse);
    }
}
