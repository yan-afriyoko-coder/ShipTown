<?php


namespace App\Modules\Api2cart;


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
     * @param array $params
     * @return array|null
     * @throws Exception
     */
    public static function getOrdersCollection(array $params)
    {
        $response = Client::GET('', 'order.list.json', $params);

        if($response->isSuccess()) {
            return $response->getResult();
        }

        Log::error('order.list.json call failed', $response->asArray());
        throw new Exception('order.list.json call failed');
    }
}
