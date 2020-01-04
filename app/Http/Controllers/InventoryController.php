<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Scopes\AuthenticatedUserScope;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index() {
        return Inventory::where("quantity_reserved", ">", 0)
            ->whereRaw("(quantity < quantity_reserved)")
            ->get();
    }

    public function store() {
        return $this->respond_OK_200();
    }
}
