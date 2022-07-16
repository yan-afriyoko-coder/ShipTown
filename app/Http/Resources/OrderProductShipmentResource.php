<?php

namespace App\Http\Resources;

use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductShipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'                        => $this->id,
            'product_id'                => $this->warehouse_id,
            'warehouse_id'              => $this->warehouse_id,
            'order_id'                  => $this->order_product_id,
            'order_product_id'          => $this->order_product_id,
            'quantity_shipped'          => $this->quantity_shipped,
            'order_product_shipment_id' => $this->order_product_shipment_id,
            'order_product'             => OrderProductResource::make($this->whenLoaded('orderProduct')),
        ];
    }
}
