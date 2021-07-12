<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductPriceResource extends JsonResource
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
            'created_at'            => $this->created_at,
            'deleted_at'            => $this->deleted_at,
            'id'                    => $this->id,
            'location_id'           => $this->location_id,
            'price'                 => $this->price,
            'product_id'            => $this->product_id,
            'sale_price'            => $this->sale_price,
            'sale_price_end_date'   => $this->sale_price_end_date,
            'sale_price_start_date' => $this->sale_price_start_date,
            'updated_at'            => $this->updated_at,
        ];
    }
}
