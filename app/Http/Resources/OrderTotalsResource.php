<?php

namespace App\Http\Resources;

use App\Models\OrderTotal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin OrderTotal
 */
class OrderTotalsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'order_id'              => $this->order_id,
            'product_line_count'    => $this->product_line_count,
            'quantity_ordered_sum'  => $this->quantity_ordered_sum,
            'quantity_to_ship_sum'  => $this->quantity_to_ship_sum,
            'total_ordered'         => $this->total_ordered,
        ];
    }
}
