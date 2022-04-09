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
            'warehouse_id'       => $this->warehouse_id,
            'warehouse_code'     => $this->warehouse_code,
            'shelf_location'     => $this->shelve_location,
            'quantity'           => $this->quantity,
            'quantity_reserved'  => $this->quantity_reserved,
            'quantity_available' => $this->quantity_available,
            'restock_level'      => $this->restock_level,
            'reorder_point'      => $this->reorder_point,
            'quantity_required'  => $this->quantity_required,
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
            'product'            => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
