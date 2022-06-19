<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryMovementStoreRequest;
use App\Http\Resources\InventoryMovementResource;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class InventoryMovementController extends Controller
{
    public function index()
    {
        $inventoryMovement = QueryBuilder::for(InventoryMovement::class)
            ->allowedIncludes([
                'product',
                'warehouse',
                'user',
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

        $inventory->quantity = $inventory->quantity + $request->get('quantity');

        $inventoryMovement = InventoryMovement::query()->make([
            'inventory_id' => $inventory->getKey(),
            'product_id' => $inventory->product_id,
            'warehouse_id' => $inventory->warehouse_id,
            'quantity_delta' => $request->get('quantity'),
            'quantity_before' => $inventory->getOriginal('quantity'),
            'quantity_after' => $inventory->quantity,
            'description'  => $request->get('description'),
            'user_id' => Auth::user()->id,
        ]);

        $inventory->save();
        $inventoryMovement->save();

        return InventoryMovementResource::collection([$inventoryMovement]);
    }
}
