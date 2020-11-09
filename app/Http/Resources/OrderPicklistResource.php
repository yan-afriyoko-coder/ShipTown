<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderPicklistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name_ordered' => $this->name_ordered,
            'sku_ordered' => $this->sku_ordered,
            'quantity_required' => 999,
            'inventory_source_quantity' => $this->inventory_source_quantity,
            'inventory_source_shelf_location' => $this->inventory_source_shelf_location,
            'product' => new ProductResource($this->whenLoaded('product'))
        ];
    }
}
