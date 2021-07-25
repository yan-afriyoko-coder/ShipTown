<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->getKey(),
            'name'              => $this->name,
            'code'              => $this->code,
            'reserves_stock'    => $this->reserves_stock,
            'order_active'      => $this->order_active,
            'sync_ecommerce'    => $this->sync_ecommerce,
        ];
    }
}
