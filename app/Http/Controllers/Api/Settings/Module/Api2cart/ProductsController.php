<?php

namespace App\Http\Controllers\Api\Settings\Module\Api2cart;

use App\Http\Controllers\Controller;
use App\Modules\Api2cart\src\Http\Requests\ProductsIndexRequest;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Products;

class ProductsController extends Controller
{
    public function index(ProductsIndexRequest $request)
    {
        $c = Api2cartConnection::query()->first();

        $sku = $request->get('sku');

        $productInfo = Products::getProductInfo($c->bridge_api_key, $sku, $c->magento_store_id, ['force_all']);

        return $this->respondOK200($productInfo);
    }
}
