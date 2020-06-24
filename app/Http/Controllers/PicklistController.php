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
            ->where('quantity_reserved','>',0)
//            ->where('shelve_location','>','B12')
            ->orderBy('shelve_location')
            ->with('product')
            ->paginate(50);
    }

    public function store(StoreRequest $request, Inventory $inventory)
    {
        $inventory->update([
            'quantity_reserved' => $inventory->quantity_reserved - $request->input('quantity_picked')
        ]);

        return new InventoryResource($inventory);
    }
}
