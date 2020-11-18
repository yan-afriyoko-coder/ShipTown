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
            'product_id' => $this->product_id,
            'name_ordered' => $this->name_ordered,
            'sku_ordered' => $this->sku_ordered,
            'total_quantity_to_pick' => (double) $this->total_quantity_to_pick,
            'inventory_source_shelf_location' => $this->inventory_source_shelf_location,
            'inventory_source_quantity' => (double) $this->inventory_source_quantity,
            'order_product_ids' => explode(',',$this->order_product_ids),
            'product' => new ProductResource($this->whenLoaded('product')),

            // to remove
            'quantity_required' => (double) $this->total_quantity_to_pick,
        ];
    }
}
