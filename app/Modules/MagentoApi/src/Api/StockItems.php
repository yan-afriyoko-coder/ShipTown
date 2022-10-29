<?php

namespace App\Modules\MagentoApi\src\Api;

use Grayloon\Magento\Api\AbstractApi;

class StockItems extends AbstractApi
{
    public function update($sku, $params)
    {
        return $this->put('/products/'.$sku.'/stockItems/0', [
            'stockItem' => $params,
        ]);
    }

    public function geta($sku)
    {
        return $this->get('/stockItems/'.$sku, []);
    }
}
