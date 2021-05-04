<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Modules\Api2cart\src\Jobs\SyncProductJob;
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
        $product->save();

        SyncProductJob::dispatch($product);

        $this->respondOK200('Product Kicked');
    }
}
