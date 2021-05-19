<?php


namespace App\Helpers;

use Grayloon\Magento\Api\AbstractApi;

class StockItems extends AbstractApi
{

    public function update($sku, $params)
    {
        return $this->put('/products/'.$sku.'/stockItems/0', [
            'stockItem' => $params
        ]);
    }
}
