<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
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
            'product_id'         => $this->product_id,
            'location_id'        => $this->location_id,
            'shelve_location'    => $this->shelve_location,
            'quantity'           => $this->quantity,
            'quantity_reserved'  => $this->quantity_reserved,
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
            'quantity_available' => $this->quantity_available,
            'product'            => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
