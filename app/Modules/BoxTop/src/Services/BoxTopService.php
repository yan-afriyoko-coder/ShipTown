<?php

namespace App\Modules\BoxTop\src\Services;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Modules\BoxTop\src\Api\ApiClient;
use App\Modules\BoxTop\src\Api\ApiResponse;
use App\Modules\BoxTop\src\Models\WarehouseStock;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;

/**
 *
 */
class BoxTopService
{
    /**
     * @param Order $order
     * @return ApiResponse
     */
    public static function postOrder(Order $order): ApiResponse
    {
        $data = self::convertToBoxTopFormat($order);

        try {
            return self::apiClient()->createWarehousePick($data);
        } catch (ClientException $exception) {
            ray($exception->getResponse()->getBody()->getContents());
            throw $exception;
        }
    }

    /**
     * @return ApiClient
     */
    public static function apiClient(): ApiClient
    {
        return new ApiClient();
    }

    /**
     * @param Order $order
     * @return array
     */
    private static function convertToBoxTopFormat(Order $order): array
    {
        $pickItems = $order->orderProducts->map(function (OrderProduct $orderProduct) {
            $apiClient = new ApiClient();
            $apiClient->getSkuQuantity($orderProduct->sku_ordered);

            /** @var WarehouseStock $warehouseStock */
            $warehouseStock = WarehouseStock::query()
                ->where(['SKUNumber' => $orderProduct->sku_ordered])
                ->where('Available', '>', '0')
                ->first();

            return [
                "Warehouse"     => $warehouseStock ? $warehouseStock->Warehouse : null,
                "SKUGroupID"    => null,
                "SKUNumber"     => $orderProduct->sku_ordered,
                "SKUName"       => $orderProduct->name_ordered,
                "Quantity"      => $orderProduct->quantity_ordered,
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
            "DeliveryContact"       => $order->shippingAddress->full_name . ' ' .$order->shippingAddress->email,
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
