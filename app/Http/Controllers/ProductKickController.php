<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Modules\Api2cart\src\Jobs\SyncProduct;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use Illuminate\Http\Request;

/**
 * Class ProductKickController.
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

        Api2cartProductLink::where(['product_id' => $product->getKey()])
            ->each(function (Api2cartProductLink $product_link) {
                SyncProduct::dispatch($product_link);
            });

        $this->respondOK200('Product Kicked');
    }
}
