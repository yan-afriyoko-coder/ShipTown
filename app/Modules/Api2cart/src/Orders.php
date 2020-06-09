<?php


namespace App\Modules\Api2cart\src;


use Exception;
use Illuminate\Support\Facades\Log;
use function Psy\debug;

/**
 * Class Orders
 * @package App\Modules\Api2cart
 */
class Orders extends Entity
{
    /**
     * @param string $store_key
     * @param array $params
     * @return array|null
     * @throws Exception
     */
    public static function get(string $store_key, array $params)
    {
        $response = Client::GET($store_key, 'order.list.json', $params);

        if($response->isSuccess()) {
            logger('Fetched orders',[
                'source' => 'API2CART',
                'count' => $response->getResult()['orders_count'],
            ]);
            return $response->getResult()['order'];
        }

        Log::error('order.list.json call failed', $response->asArray());
        throw new Exception('order.list.json call failed - '.$response->getReturnMessage());
    }
}
