<?php

namespace App\Modules\Api2cart\src\Api;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
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
     * @throws Exception|GuzzleException
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

    /**
     * @param $store_key
     * @param $params
     * @throws Exceptions\RequestException
     * @throws GuzzleException
     */
    public static function update($store_key, $params): RequestResponse
    {
        return Client::POST($store_key, 'order.update.json', $params);
    }

    /**
     * @throws Exceptions\RequestException
     * @throws GuzzleException
     */
    public static function statuses($store_key, $params): RequestResponse
    {
        return Client::GET($store_key, 'order.status.list.json', $params);
    }
}
