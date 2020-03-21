<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryRequest;
use App\Http\Resources\InventoryResource;
use App\Http\Resources\InventoryResourceCollection;
use App\Http\Resources\ProductResource;
use App\Models\Inventory;
use App\Models\Product;
use App\Scopes\AuthenticatedUserScope;
use Illuminate\Http\Request;
use Illuminate\Session\Store;

class InventoryController extends Controller
{
    public function index()
    {
        return Product::query()
            ->whereHas('inventory', function($query) {
                $query->where('quantity_reserved', '>', 0);
            })
            ->get()
            ->load('inventory');
    }

    public function store(StoreInventoryRequest $request)
    {
        $product = Product::query()->where('sku', '=', $request->sku)->first();

        if(!$product) {
            return $this->respond_NotFound("SKU not found!");
        }

        $update = $request->all();

        $update['product_id'] = $product->id;

        $inventory = Inventory::updateOrCreate([
            "product_id" => $update['product_id'],
            "location_id" => $update['location_id'],
            ]
        , $update);

        return $this->respond_OK_200();
    }
}
