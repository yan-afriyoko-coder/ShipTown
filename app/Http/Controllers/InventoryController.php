<?php

namespace App\Http\Controllers;

use App\Http\Resources\InventoryResource;
use App\Http\Resources\InventoryResourceCollection;
use App\Http\Resources\ProductResource;
use App\Models\Inventory;
use App\Models\Product;
use App\Scopes\AuthenticatedUserScope;
use Illuminate\Http\Request;

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

    public function store() {
        return $this->respond_OK_200();
    }
}
