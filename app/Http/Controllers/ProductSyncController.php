<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductSyncController extends Controller
{
    public function index($sku) {

        $product = Product::query()->where("sku", $sku)->firstOrFail();

        $this->respond_OK_200();
    }
}
