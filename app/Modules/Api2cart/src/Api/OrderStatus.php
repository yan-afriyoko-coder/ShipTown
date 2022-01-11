<?php


namespace App\Modules\Api2cart\src\Api;

class OrderStatus
{
    /**
     */
    public static function list(string $store_key, array $params = []): RequestResponse
    {
        return Client::GET($store_key, 'order.status.list.json', $params);
    }
}
