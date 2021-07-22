<?php

namespace App\Modules\Api2cart\src\Api;

use App\Modules\Api2cart\src\Exceptions\RequestException;
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
     * @param array $params
     *
     * @return array|null
     * @throws Exception|GuzzleException
     *
     */
    public static function get(string $store_key, array $params)
    {
        $response = Client::GET($store_key, 'order.list.json', $params);

        if ($response->isSuccess()) {
            logger('Fetched orders', [
                'source' => 'API2CART',
                'count' => $response->getResult()['orders_count'],
            ]);

            return $response->getResult()['order'];
        }

        Log::error('order.list.json call failed', $response->asArray());

        throw new Exception('order.list.json call failed - ' . $response->getReturnMessage());
    }

    /**
     * @throws GuzzleException|RequestException
     */
    public static function list(string $store_key, array $params): RequestResponse
    {
        return Client::GET($store_key, 'order.list.json', $params);
    }

    /**
     * @param $store_key
     * @param $params
     * @return RequestResponse
     * @throws GuzzleException
     * @throws RequestException
     */
    public static function update($store_key, $params): RequestResponse
    {
        return Client::POST($store_key, 'order.update.json', $params);
    }

    /**
     *
     */
    public static function statuses($store_key, $params): RequestResponse
    {
        return Client::GET($store_key, 'order.status.list.json', $params);
    }
}
