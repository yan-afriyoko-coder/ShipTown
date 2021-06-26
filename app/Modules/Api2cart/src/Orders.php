<?php

namespace App\Modules\Api2cart\src;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class Orders.
 */
class Orders extends Entity
{
    /**
     * @param string $store_key
     * @param array  $params
     *
     * @throws Exception
     *
     * @return array|null
     */
    public static function get(string $store_key, array $params)
    {
        $response = Client::GET($store_key, 'order.list.json', $params);

        if ($response->isSuccess()) {
            logger('Fetched orders', [
                'source' => 'API2CART',
                'count'  => $response->getResult()['orders_count'],
            ]);

            return $response->getResult()['order'];
        }

        Log::error('order.list.json call failed', $response->asArray());

        throw new Exception('order.list.json call failed - '.$response->getReturnMessage());
    }
}
