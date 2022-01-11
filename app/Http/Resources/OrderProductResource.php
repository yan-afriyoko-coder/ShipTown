<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
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
            'order_id'                        => $this->order_id,
            'product_id'                      => $this->product_id,
            'sku_ordered'                     => $this->sku_ordered,
            'name_ordered'                    => $this->name_ordered,
            'price'                           => $this->price,
            'quantity_ordered'                => $this->quantity_ordered,
            'quantity_split'                  => $this->quantity_split,
            'quantity_picked'                 => $this->quantity_picked,
            'quantity_skipped_picking'        => $this->quantity_skipped_picking,
            'quantity_shipped'                => $this->quantity_shipped,
            'quantity_to_ship'                => $this->quantity_to_ship,
            'inventory_source_location_id'    => $this->inventory_source_location_id,
            'inventory_source_shelf_location' => $this->inventory_source_shelf_location,
            'inventory_source_quantity'       => $this->inventory_source_quantity,
            'order'                           => new OrderResource($this->whenLoaded('order')),
            'product'                         => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
