<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed is_packed
 */
class PacklistResource extends JsonResource
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
            'product_id' => $this->product_id,
            'location_id' => $this->location_id,
            'shelve_location' => $this->shelve_location,
            'quantity_requested' => $this->quantity_requested,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'packed_at' => $this->packed_at,
            'is_packed' => $this->is_packed,
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
