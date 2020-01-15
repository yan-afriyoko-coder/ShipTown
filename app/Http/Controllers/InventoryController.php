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
    public function index() {

        return ProductResource::collection(
            Product::query()
                ->limit(50)
                ->get()
                ->load('inventory')
        );

        return InventoryResource::collection(
            Inventory::all()
//                ->load('product')
        );
//        return Inventory::query()
//            //->with(Product::class)
//            //->where("quantity_reserved", ">", 0)
//            //->whereRaw("(quantity < quantity_reserved)")
//            ->get();
    }

    public function store(StoreInventoryRequest $request) {
        $product = Product::query()->where('sku', '=', $request->sku);
        return $this->respond_OK_200();
    }
}
