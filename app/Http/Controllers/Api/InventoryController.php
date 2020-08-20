<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventoryRequest;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->get('per_page') == 'all') {
            return Product::whereHas('inventory', function ($query) {
                    $query->where('quantity_reserved', '>', 0);
            })
                ->get()
                ->load('inventory');
        } else {
            return Product::whereHas('inventory', function ($query) {
                    $query->where('quantity_reserved', '>', 0);
            })
                ->when($request->has('q'), function ($query) use ($request) {
                    return $query
                        ->where('sku', 'like', '%' . $request->get('q') . '%')
                        ->orWhere('name', 'like', '%' . $request->get('q') . '%');
                })
                ->when($request->has('sort'), function ($query) use ($request) {
                        return $query
                            ->orderBy($request->get('sort'), $request->get('order', 'asc'));
                })
                ->with('inventory')
                ->paginate(100);
        }
    }

    public function store(StoreInventoryRequest $request)
    {
        $product = Product::query()->where('sku', '=', $request->sku)->first();

        if (!$product) {
            return $this->respondNotFound("SKU not found!");
        }

        $update = $request->all();

        $update['product_id'] = $product->id;

        $inventory = Inventory::updateOrCreate(
            [
            "product_id" => $update['product_id'],
            "location_id" => $update['location_id'],
            ],
            $update
        );

        return $this->respondOK200();
    }
}
