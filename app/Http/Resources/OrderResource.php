<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            "id" => $this->id,
            "order_number" => $this->order_number,
            "total" => $this->total,
            "total_paid" => $this->total_paid,
            "shipping_address_id" => $this->shipping_address_id,
            "order_placed_at" => $this->order_placed_at,
            "order_closed_at" => $this->order_closed_at,
            "product_line_count" => $this->product_line_count,
            "total_quantity_ordered"=> $this->total_quantity_ordered,
            "status_code" => $this->status_code,
            "picked_at" => $this->picked_at,
            "packed_at" => $this->packed_at,
            "packer_user_id" => $this->packer_user_id,
            "deleted_at" => $this->deleted_at,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "raw_import" => $this->raw_import,
            "order_id" => $this->order_id,
            "min_shelf_location" => $this->min_shelf_location,
            "max_shelf_location" => $this->max_shelf_location,
            "is_picked" => $this->is_picked,
            "is_packed" => $this->is_packed,

            "activities" => new ActivityResource($this->whenLoaded('activities')),
            "stats" => new JsonResource($this->whenLoaded('stats')),
            "shipping_address" => new JsonResource($this->whenLoaded('shipping_address')),
            "order_shipments" => new JsonResource($this->whenLoaded('order_shipments')),
            "order_products" => new JsonResource($this->whenLoaded('order_products')),
            "packer" => new UserResource($this->whenLoaded('packer')),
            "order_comments" => new JsonResource($this->whenLoaded('order_comments')),
        ];
    }
}
