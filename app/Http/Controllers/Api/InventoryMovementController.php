<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryMovementStoreRequest;
use App\Http\Resources\InventoryMovementResource;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Services\InventoryService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class InventoryMovementController extends Controller
{
    public function index()
    {
        $inventoryMovement = QueryBuilder::for(InventoryMovement::class)
            ->allowedFilters([
                AllowedFilter::exact('description'),
                AllowedFilter::exact('warehouse_id'),
            ])
            ->allowedIncludes([
                'inventory',
                'product',
                'warehouse',
                'user',
            ])
            ->allowedSorts([
                'id',
            ]);

        return InventoryMovementResource::collection($this->getPaginatedResult($inventoryMovement));
    }

    public function store(InventoryMovementStoreRequest $request): AnonymousResourceCollection
    {
        /** @var Inventory $inventory */
        $inventory = Inventory::query()
            ->where([
                'product_id' => $request->get('product_id'),
                'warehouse_id' => $request->get('warehouse_id'),
            ])
            ->first();

        $inventoryMovement = InventoryService::adjustQuantity(
            $inventory,
            data_get($request->validated(), 'quantity'),
            data_get($request->validated(), 'description'),
        );

        return InventoryMovementResource::collection([$inventoryMovement]);
    }
}
