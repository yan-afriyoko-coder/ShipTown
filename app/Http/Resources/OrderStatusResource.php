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
            'order_active'      => $this->order_active,
            'reserves_stock'    => $this->reserves_stock,
            'hidden'            => $this->hidden,
            'sync_ecommerce'    => $this->sync_ecommerce,
        ];
    }
}
