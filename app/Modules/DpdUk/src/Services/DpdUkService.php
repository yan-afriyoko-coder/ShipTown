<?php

namespace App\Modules\DpdUk\src\Services;

use App\Models\OrderShipment;
use App\Modules\DpdUk\src\Api\Client;
use App\Modules\DpdUk\src\Models\Connection;
use App\Modules\DpdUk\src\Models\GetShippingLabelResponse;
use App\Modules\PrintNode\src\PrintNode;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\GuzzleException;

class DpdUkService
{
    /**
     * @param OrderShipment $orderShipment
     * @param Connection $connection
     * @return array
     */
    private static function generatePayload(OrderShipment $orderShipment, Connection $connection): array
    {
        $collectionAddress = $connection->collectionAddress;
        $shippingAddress = $orderShipment->order->shippingAddress;

        return [
            "jobId" => null,
            "collectionOnDelivery" => false,
            "invoice" => null,
            "collectionDate" => Carbon::today(),
            "consolidate" => false,
            "consignment" => [
                [
                    "consignmentNumber" => null,
                    "consignmentRef" => null,
                    "parcel" => [],
                    "collectionDetails" => [
                        "contactDetails" => [
                            "contactName"   => $collectionAddress->full_name,
                            "telephone"     => $collectionAddress->phone
                        ],
                        "address" => [
                            "organisation"  => $collectionAddress->company,
                            "countryCode"   => $collectionAddress->country_code,
                            "postcode"      => $collectionAddress->postcode,
                            "street"        => $collectionAddress->address1,
                            "locality"      => $collectionAddress->address2,
                            "town"          => $collectionAddress->city,
                            "county"        => $collectionAddress->state_code,
                        ],
                    ],
                    "deliveryDetails" => [
                        "contactDetails" => [
                            "contactName"   => $shippingAddress->full_name,
                            "telephone"     => $shippingAddress->phone
                        ],
                        "address" => [
                            "organisation"  => $shippingAddress->company,
                            "countryCode"   => $shippingAddress->country_code,
                            "postcode"      => $shippingAddress->postcode,
                            "street"        => $shippingAddress->address1,
                            "locality"      => $shippingAddress->address2,
                            "town"          => $shippingAddress->city,
                            "county"        => $shippingAddress->state_code
                        ],
                        "notificationDetails" => [
                            "email"         => $shippingAddress->email,
                            "mobile"        => $shippingAddress->phone
                        ]
                    ],
                    "networkCode" => "1^12",
                    "numberOfParcels" => 1,
                    "totalWeight" => 10,
                    "shippingRef1" => "TEST_#" . $orderShipment->order->order_number,
                    "shippingRef2" => "",
                    "shippingRef3" => "",
                    "customsValue" => null,
                    "deliveryInstructions" => "",
                    "parcelDescription" => "",
                    "liabilityValue" => null,
                    "liability" => false,
                ],
            ],
        ];
    }

    /**
     * @param OrderShipment $orderShipment
     * @param Connection $connection
     * @return string|null
     * @throws GuzzleException
     */
    public static function printNewLabel(OrderShipment $orderShipment, Connection $connection): ?string
    {
        $labelResponse = self::makeNewLabel($connection, $orderShipment);

        ray(base64_encode($labelResponse->getContent()));

        if (isset(auth()->user()->printer_id)) {
            return PrintNode::printRaw(
                base64_encode($labelResponse->getContent()),
                auth()->user()->printer_id
            );
        }

        return null;
    }

    /**
     * @param Connection $connection
     * @param OrderShipment $orderShipment
     * @return GetShippingLabelResponse
     * @throws GuzzleException
     * @throws Exception
     */
    private static function makeNewLabel(Connection $connection, OrderShipment $orderShipment): GetShippingLabelResponse
    {
        $dpd = new Client($connection);

        $payload = self::generatePayload($orderShipment, $connection);

        $shipmentResponse = $dpd->createShipment($payload);

        if ($shipmentResponse->errors()) {
            $shipmentResponse->errors()->each(function ($error) {
                throw new Exception(
                    $error['obj'] . ': ' . $error['errorMessage'],
                    $error['errorCode']
                );
            });
        }

        return $dpd->getShipmentLabel($shipmentResponse->getShipmentId());
    }
}
