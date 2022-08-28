<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StocktakesStoreRequest;
use App\Http\Resources\InventoryMovementResource;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StocktakesController extends Controller
{
    public function store(StocktakesStoreRequest $request): AnonymousResourceCollection
    {
        $inventoryMovement = null;

        DB::transaction(function () use ($request, &$inventoryMovement) {
            /** @var Inventory $inventory */
            $inventory = Inventory::where([
                    'product_id' => $request->get('product_id'),
                    'warehouse_id' => $request->get('warehouse_id'),
                ])
                ->first();

            $quantityDelta = $request->get('new_quantity') - $inventory->quantity;

            /** @var InventoryMovement $inventoryMovement */
            $inventoryMovement = InventoryMovement::query()->create([
                'inventory_id' => $inventory->id,
                'product_id' => $inventory->product_id,
                'warehouse_id' => $inventory->warehouse_id,
                'quantity_before' => $inventory->quantity,
                'quantity_delta' => $quantityDelta,
                'quantity_after' => $inventory->quantity + $quantityDelta,
                'description' => 'stocktake',
                'user_id' => Auth::id(),
            ]);

            $inventory->update([
                'quantity' => $inventoryMovement->quantity_after,
                'last_counted_at' => now()
            ]);
        });

        return InventoryMovementResource::collection([$inventoryMovement]);
    }
}
