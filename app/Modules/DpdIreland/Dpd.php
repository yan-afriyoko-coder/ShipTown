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
        $consignment = self::getConsignment($order);

        $preAdvice = self::getPreAdvice($consignment);

        self::saveOrderShipment($order, $preAdvice, $user);

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
            'PostCode' => $shipping_address->postcode,
            'CountryCode' => $shipping_address->country_code,
        ];
    }

    /**
     * @param Order $order
     * @return Consignment
     * @throws src\Exceptions\ConsignmentValidationException
     */
    private static function getConsignment(Order $order): Consignment
    {
        $shipping_address = $order->shippingAddress()->first();

        return new Consignment([
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
    }

    /**
     * @param Order $order
     * @param PreAdvice $preAdvice
     * @param User|null $user
     * @throws Exception
     */
    private static function saveOrderShipment(Order $order, PreAdvice $preAdvice, ?User $user): void
    {
        retry(15, function () use ($order, $preAdvice, $user) {
            $order->orderShipments()->create([
                'user_id' => $user ? $user->getKey() : null,
                'shipping_number' => $preAdvice->trackingNumber(),
            ]);
        }, 150);
    }
}
