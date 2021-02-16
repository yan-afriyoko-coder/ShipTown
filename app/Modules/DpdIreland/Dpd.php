<?php


namespace App\Modules\DpdIreland;

use App\Models\Order;
use App\Models\OrderAddress;
use App\Modules\DpdIreland\src\Client;
use App\Modules\DpdIreland\src\Consignment;
use App\Modules\DpdIreland\src\Exceptions\AuthorizationException;
use App\Modules\DpdIreland\src\Exceptions\PreAdviceRequestException;
use App\Modules\DpdIreland\src\Responses\PreAdvice;
use App\User;
use Composer\Cache;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class Dpd
 * @package App\Modules\Dpd\src
 */
class Dpd
{
    const NOT_AUTH = 'NOT_AUTH';

    /**
     * @param Order $order
     * @param User|null $user
     * @return PreAdvice
     * @throws PreAdviceRequestException
     * @throws src\Exceptions\ConsignmentValidationException
     * @throws Exception
     * @throws AuthorizationException
     */
    public static function shipOrder(Order $order, User $user = null): PreAdvice
    {
        $shipping_address = $order->shippingAddress()->first();

        $consignment = new Consignment([
            'RecordID' => $order->order_number,
            'DeliveryAddress' => self::getDeliveryAddress($shipping_address),
            'ConsignmentReference' => $order->order_number,
            'References' => [
                'Reference' => [
                    'ReferenceName' => 'Order',
                    'ReferenceValue' => $order->order_number,
                    'ParcelNumber' => 1,
                ]
            ]
        ]);

        $preAdvice = self::getPreAdvice($consignment);

        retry(15, function () use ($order, $preAdvice, $user){
            $order->orderShipments()->create([
                'user_id' => $user ? $user->getKey() : null,
                'shipping_number' => $preAdvice->trackingNumber(),
            ]);
        }, 150);

        return $preAdvice;
    }

    /**
     * @param Consignment $consignment
     * @return PreAdvice
     * @throws PreAdviceRequestException
     * @throws AuthorizationException
     */
    public static function getPreAdvice(Consignment $consignment): PreAdvice
    {
        $response = Client::postXml($consignment->toXml());

        $preAdvice = new PreAdvice($response->getBody()->getContents());

        if($preAdvice->isNotSuccess()) {
            Log::error('DPD PreAdvice request failed', [
                'xml_received' => $preAdvice->toString(),
                'xml_sent' => $consignment->toString(),
            ]);

            if ($preAdvice->getPreAdviceErrorCode() === self::NOT_AUTH) {
                Client::clearCache();
                throw new AuthorizationException($preAdvice->getPreAdviceErrorDetails());
            } else {
                throw new PreAdviceRequestException($preAdvice->consignment()['RecordErrorDetails']);
            }
        }

        return $preAdvice;
    }

    /**
     * @param OrderAddress $shipping_address
     * @return array
     */
    private static function getDeliveryAddress(OrderAddress $shipping_address): array
    {
        return [
            'Contact' => $shipping_address->full_name,
            'ContactTelephone' => $shipping_address->phone,
            'ContactEmail' => '',
            'AddressLine1' => $shipping_address->address1,
            'AddressLine2' => $shipping_address->address2,
            'AddressLine3' => $shipping_address->city,
            'AddressLine4' => $shipping_address->state_name ?: $shipping_address->city,
            'CountryCode' => $shipping_address->country_code,
        ];
    }
}
