<?php

namespace App\Modules\DpdUk\src\Services;

use App\Abstracts\ShippingServiceAbstract;
use App\Models\Order;
use App\Models\OrderShipment;
use App\Modules\DpdUk\src\Api\ApiClient;
use App\Modules\DpdUk\src\Models\Connection;
use App\Modules\PrintNode\src\PrintNode;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class NextDayShippingService extends ShippingServiceAbstract
{
    /**
     * @var Connection
     */
    private Connection $connection;

    private ApiClient $apiClient;

    private OrderShipment $shipment;

    public function __construct()
    {
        $this->connection = Connection::firstOrFail();

        $this->apiClient = new ApiClient($this->connection);

        $this->shipment = new OrderShipment();
    }

    /**
     * @throws Exception
     */
    public function ship(int $order_id): AnonymousResourceCollection
    {
        /** @var Order $order */
        $order = Order::findOrFail($order_id);

        self::printNewLabel($order);

        return JsonResource::collection([]);
    }

    /**
     * @param Order $order
     * @return array
     */
    private function convertToDpdUkFormat(Order $order): array
    {
        try {
            /** @var User $user */
            $user = auth()->user();

            if ($user && isset($user->warehouse->address)) {
                $collectionAddress = $user->warehouse->address;
            } else {
                $collectionAddress = $this->connection->collectionAddress;
            }
        } catch (Exception $exception) {
            $collectionAddress = $this->connection->collectionAddress;
        }

        $shippingAddress = $order->shippingAddress;

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
                            "countryCode"   => self::replaceArray(['GBR' => "GB"], $collectionAddress->country_code),
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
                            "countryCode"   => self::replaceArray(['GBR' => "GB"], $shippingAddress->country_code),
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
                    "shippingRef1" => "#" . $order->order_number,
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

    public function replaceArray(array $replaceArray, string $subject)
    {
        return str_replace(array_keys($replaceArray), array_values($replaceArray), $subject);
    }

    /**
     * @param Order $order
     * @return string|null
     * @throws Exception
     */
    public function printNewLabel(Order $order): ?string
    {
        $this->makeNewLabel($order);

        if (isset(auth()->user()->printer_id)) {
            return PrintNode::printRaw(
                $this->shipment->base64_pdf_labels,
                auth()->user()->printer_id
            );
        }

        return null;
    }

    /**
     * @param Order $order
     * @return void
     * @throws Exception
     */
    private function makeNewLabel(Order $order): void
    {
        $payload = $this->convertToDpdUkFormat($order);

        $dpdShipment = $this->apiClient->createShipment($payload);
        $dpdShippingLabel = $this->apiClient->getShipmentLabel($dpdShipment->getShipmentId());

        $this->shipment->order_id = $order->getKey();
        $this->shipment->carrier = 'DPD UK';
        $this->shipment->service = 'overnight';
        $this->shipment->shipping_number = $dpdShipment->getConsignmentNumber();
        $this->shipment->tracking_url = $this->generateTrackingUrl($this->shipment);
        $this->shipment->base64_pdf_labels = base64_encode($dpdShippingLabel->response->content);
        $this->shipment->save();
    }

    /**
     * @param OrderShipment $orderShipment
     * @return string
     */
    private function generateTrackingUrl(OrderShipment $orderShipment): string
    {
        $baseUlr = 'https://track.dpd.co.uk/search';
        $referenceParam = 'reference=' . $orderShipment->shipping_number;
        $postcodeParam = 'postcode=' . $orderShipment->order->shippingAddress->postcode;

        return $baseUlr .'?'. $referenceParam .'&'. $postcodeParam;
    }
}
