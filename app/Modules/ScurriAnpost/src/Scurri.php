<?php


namespace App\Modules\ScurriAnpost\src;

use App\Models\Order;
use App\Models\OrderShipment;
use App\Modules\ScurriAnpost\src\Api\Client;
use Exception;

class Scurri
{
    /**
     * @param Order $order
     * @return OrderShipment
     * @throws Exception
     */
    public static function createOrderShipment(Order $order): OrderShipment
    {
        $consignment_id = Client::createSingleConsignment([
            "order_number" => $order->order_number,
            "recipient" => [
                "address" => [
                    "country" => $order->shippingAddress->country_code,
                    "postcode" => $order->shippingAddress->postcode,
                    "city" => $order->shippingAddress->city,
                    "address1" => $order->shippingAddress->address1,
                    "address2" => $order->shippingAddress->address2,
                    "state" => $order->shippingAddress->state_code
                ],
                "contact_number" => $order->shippingAddress->phone,
                "email_address" => "",
                "company_name" => $order->shippingAddress->company,
                "name" => $order->shippingAddress->full_name,
            ],
            "packages" => [
                [
                    "items" => [
                        [
                            "sku" => "n/a",
                            "quantity" => 1,
                            "name" => "Shipment",
                            "value" => 1,
                        ]
                    ],
                    "length" => 50,
                    "height" => 50,
                    "width" => 50,
                    "reference" => ""
                ],
            ],
        ]);

        // in order to obtain shipping number we need to generate documents
        $documents = Client::getDocuments($consignment_id);

        // we need to refresh it in order to obtain shipping number
        $consignment = Client::getSingleConsignment($consignment_id)->json();

        $orderShipment = new OrderShipment([
            'order_id' => $order->getKey(),
            'carrier' => 'AnPost',
            'service' => $consignment['service'],
            'shipping_number' => $consignment['consignment_number'],
            'tracking_url' => 'https://www.anpost.com/Commerce/Track?item=' . $consignment['tracking_url'],
            'base64_pdf_labels' => base64_encode($documents->getLabels()),
        ]);

        $orderShipment->save();

        return $orderShipment;
    }
}
