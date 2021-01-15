<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Api2cartService;
use Illuminate\Http\Request;

/**
 * Class ProductKickController
 * @package App\Http\Controllers
 */
class ProductKickController extends Controller
{
    /**
     * @param Request $request
     * @param $sku
     */
    public function index(Request $request, $sku)
    {
        $product = Product::query()->where(['sku' => $sku])->first();

        Api2cartService::dispatchSyncProductJob($product);

        $this->respondOK200('Product Kicked');
    }
}
