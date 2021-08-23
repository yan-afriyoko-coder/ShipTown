<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductShipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                              => $this->id,
            'order_product_id'                => $this->order_product_id,
            'quantity_shipped'                => $this->quantity_shipped,
            'order_product_shipment_id'       => $this->order_product_shipment_id,
        ];
    }
}
