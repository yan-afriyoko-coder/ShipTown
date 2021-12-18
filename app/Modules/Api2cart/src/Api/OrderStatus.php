<?php


namespace App\Modules\Api2cart\src\Api;

use App\Modules\Api2cart\src\Exceptions\RequestException;

class OrderStatus
{
    /**
     * @throws RequestException
     */
    public static function list(string $store_key, array $params = []): RequestResponse
    {
        return Client::GET($store_key, 'order.status.list.json', $params);
    }
}
