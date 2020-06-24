<?php

namespace App\Http\Controllers;

use App\Http\Requests\Picklist\StoreRequest;
use App\Models\Inventory;
use App\Http\Resources\InventoryResource;
use App\Models\Product;
use Illuminate\Http\Request;

class PicklistController extends Controller
{
    public function index(Request $request)
    {
        return Inventory::query()
            ->with('product')
            ->paginate(10);
    }

    public function store(StoreRequest $request, Inventory $inventory)
    {
        $inventory->update([
            'quantity' => $inventory->quantity - $request->input('quantity'),
            'quantity_reserved' => $inventory->quantity_reserved - $request->input('quantity')
        ]);

        return new InventoryResource($inventory);
    }
}
