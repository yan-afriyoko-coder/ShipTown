<?php

namespace App\Http\Controllers\Api\Settings\Module\Api2cart;

use App\Http\Controllers\Controller;
use App\Modules\Api2cart\src\Http\Requests\ProductsIndexRequest;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Products;
use Exception;

class ProductsController extends Controller
{
    public function index(ProductsIndexRequest $request)
    {
        $productInfo = null;
        $c = Api2cartConnection::query()->first();

        $sku = $request->get('sku');

        try {
            $productInfo = Products::getProductInfo($c, $sku, ['force_all']);
        } catch (Exception $exception) {
            $this->setStatusCode(500)->respond('Server error occurred, see logs for details');
        }

        return $this->respondOK200($productInfo);
    }
}
