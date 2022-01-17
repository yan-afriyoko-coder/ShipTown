<?php

namespace App\Modules\DpdIreland;

use App\Models\Order;
use App\Models\OrderShipment;
use App\Modules\DpdIreland\src\Client;
use App\Modules\DpdIreland\src\Consignment;
use App\Modules\DpdIreland\src\Exceptions\AuthorizationException;
use App\Modules\DpdIreland\src\Exceptions\PreAdviceRequestException;
use App\Modules\DpdIreland\src\Responses\PreAdvice;
use App\User;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class Dpd.
 */
class Dpd
{
    const NOT_AUTH = 'NOT_AUTH';

    /**
     * @param Order $order
     * @param User|null $user
     *
     * @return PreAdvice
     * @throws src\Exceptions\ConsignmentValidationException
     * @throws Exception
     * @throws AuthorizationException
     * @throws GuzzleException
     *
     * @throws PreAdviceRequestException|GuzzleException
     */
    public static function shipOrder(Order $order, User $user = null): PreAdvice
    {
        $consignment = self::getConsignmentData($order);

        $preAdvice = self::getPreAdvice($consignment);

        $shipment = self::saveOrderShipment($order, $preAdvice, $user);

        logger('DPD PreAdvice generated', [
            'consignment' => $consignment->toArray(),
            'preAdvice'   => $preAdvice->toArray(),
            'shipment'   => $shipment->toArray(),
        ]);

        return $preAdvice;
    }

    /**
     * @param Consignment $consignment
     *
     * @return PreAdvice
     *
     * @throws PreAdviceRequestException|GuzzleException|AuthorizationException
     */
    public static function getPreAdvice(Consignment $consignment): PreAdvice
    {
        $response = Client::postXml($consignment->toXml());

        $preAdvice = new PreAdvice($response);

        if ($preAdvice->isSuccess()) {
            return $preAdvice;
        }

        Log::error('DPD PreAdvice request failed', [
            'xml_received' => $preAdvice->toString(),
            'xml_sent'     => $consignment->toString(),
        ]);

        if ($preAdvice->getPreAdviceErrorCode() === self::NOT_AUTH) {
            Client::clearCache();
            throw new AuthorizationException($preAdvice->getPreAdviceErrorDetails());
        } else {
            throw new PreAdviceRequestException($preAdvice->consignment()['RecordErrorDetails']);
        }
    }

    /**
     * @param Order $order
     *
     * @throws src\Exceptions\ConsignmentValidationException
     *
     * @return Consignment
     */
    private static function getConsignmentData(Order $order): Consignment
    {
        $shipping_address = $order->shippingAddress()->first();

        return new Consignment([
            'RecordID'             => $order->order_number,
            'ConsignmentReference' => $order->order_number,
            'DeliveryAddress'      => [
                'Contact'          => $shipping_address->full_name,
                'ContactTelephone' => $shipping_address->phone,
                'ContactEmail'     => $shipping_address->email,
                'AddressLine1'     => $shipping_address->address1,
                'AddressLine2'     => $shipping_address->address2,
                'AddressLine3'     => $shipping_address->city,
                'AddressLine4'     => $shipping_address->state_name ?: $shipping_address->city,
                'PostCode'         => $shipping_address->postcode,
                'CountryCode'      => $shipping_address->country_code,
            ],
            'References'           => [
                'Reference' => [
                    'ReferenceName'  => 'Order',
                    'ReferenceValue' => $order->order_number,
                    'ParcelNumber'   => 1,
                ],
            ],
        ]);
    }

    /**
     * @param Order $order
     * @param PreAdvice $preAdvice
     * @param User|null $user
     *
     * @return OrderShipment
     * @throws Exception
     */
    public static function saveOrderShipment(Order $order, PreAdvice $preAdvice, ?User $user): OrderShipment
    {
        return retry(15, function () use ($order, $preAdvice, $user) {
            return $order->orderShipments()->create([
                'user_id'         => $user ? $user->getKey() : null,
                'carrier'         => 'DPD Ireland',
                'shipping_number' => $preAdvice->trackingNumber(),
                'tracking_url' => 'https://dpd.ie/tracking?consignmentNumber='.$preAdvice->trackingNumber(),
            ]);
        }, 150);
    }
}
