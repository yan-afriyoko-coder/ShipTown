<?php

namespace App\Modules\BoxTop\src\Services;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Modules\BoxTop\src\Api\ApiClient;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;

/**
 *
 */
class BoxTopService
{
    /**
     * @param Order $order
     * @param string $warehouse
     * @return bool
     */
    public static function postOrder(Order $order, string $warehouse): bool
    {
        $data = self::convertToBoxTopFormat($order, $warehouse);

        try {
            $apiClient = new ApiClient();
            return 201 === $apiClient->createWarehousePick($data)->http_response->getStatusCode();
        } catch (ClientException $exception) {
            ray($exception->getResponse()->getBody()->getContents());
        }

        return false;
    }

    /**
     * @param Order $order
     * @param string $warehouse
     * @return array
     */
    private static function convertToBoxTopFormat(Order $order, string $warehouse): array
    {
        $pickItems = $order->orderProducts->map(function (OrderProduct $orderProduct) use ($warehouse) {

            $apiClient = new ApiClient();
            $apiClient->getSkuQuantity($orderProduct->sku_ordered);

            return [
                "Warehouse"     => $warehouse,
                "SKUGroupID"    => null,
                "SKUNumber"     => $orderProduct->sku_ordered,
                "SKUName"       => $orderProduct->name_ordered,
                "Quantity"      => 1,
                "Add1"          => "",
                "Add2"          => "",
                "Add3"          => "",
                "Add4"          => "",
                "Add5"          => "",
                "Add6"          => "",
                "Comments"      => ""
            ];
        })->toArray();

        return [
            "DeliveryCompanyName"   => $order->shippingAddress->company,
            "DeliveryAddress1"      => $order->shippingAddress->address1,
            "DeliveryAddress2"      => $order->shippingAddress->address2,
            "DeliveryCity"          => $order->shippingAddress->city,
            "DeliveryCounty"        => $order->shippingAddress->state_code,
            "DeliveryPostCode"      => $order->shippingAddress->postcode,
            "DeliveryCountry"       => $order->shippingAddress->country_code,
            "DeliveryPhone"         => $order->shippingAddress->phone,
            "DeliveryContact"       => $order->shippingAddress->full_name,
            "OutboundRef"           => "WEB_". $order->order_number,
            "ReleaseDate"           => Carbon::today()->addDays(2),
            "DeliveryDate"          => Carbon::today()->addDays(2),
            "DeliveryTime"          => "",
            "Haulier"               => "",
            "PickItems"             => $pickItems,
            "BranchID"              => 513,
            "CustomerID"            => "BELLABAB",
            "NOP"                   => 1,
            "Weight"                => 1,
            "Cube"                  => 1,
            "CustRef"               => "WEB_". $order->order_number,
            "Remarks"               => ""
        ];
    }
}
