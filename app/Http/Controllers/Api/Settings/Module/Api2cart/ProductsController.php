<?php

namespace App\Http\Controllers\Api\Settings\Module\Api2cart;

use App\Http\Controllers\Controller;
use App\Modules\Api2cart\src\Exceptions\RequestException;
use App\Modules\Api2cart\src\Http\Requests\ProductsIndexRequest;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Products;

class ProductsController extends Controller
{
    /**
     * @throws RequestException
     */
    public function index(ProductsIndexRequest $request)
    {
        $connection = Api2cartConnection::query()->first();

        $sku = $request->get('sku');

        $productInfo = Products::getProductInfo($connection, $sku, ['force_all']);

        $this->respondOK200($productInfo);
    }
}
