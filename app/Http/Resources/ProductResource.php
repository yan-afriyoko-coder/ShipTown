<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'sku'                             => $this->sku,
            'name'                            => $this->name,
            'price'                           => $this->price,
            'sale_price'                      => $this->sale_price,
            'sale_price_start_date'           => $this->sale_price_start_date,
            'sale_price_end_date'             => $this->sale_price_end_date,
            'quantity'                        => $this->quantity,
            'quantity_reserved'               => $this->quantity_reserved,
            'deleted_at'                      => $this->deleted_at,
            'created_at'                      => $this->created_at,
            'updated_at'                      => $this->updated_at,
            'quantity_available'              => $this->quantity_available,
            'inventory_source_location_id'    => $this->inventory_source_location_id,
            'inventory_source_product_id'     => $this->inventory_source_product_id,
            'inventory_source_shelf_location' => $this->inventory_source_shelf_location,
            'inventory_source_quantity'       => $this->inventory_source_quantity,
            'inventory'                       => InventoryResource::collection($this->whenLoaded('inventory')),
            'aliases'                         => ProductAliasResource::collection($this->whenLoaded('aliases')),
            'prices'                          => ProductPriceResource::collection($this->whenLoaded('prices')),
            'tags'                            => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
