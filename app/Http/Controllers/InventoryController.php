<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Scopes\AuthenticatedUserScope;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index() {
        return Product::all();
    }
}
