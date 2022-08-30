<?php

namespace App\Http\Resources;

use App\Models\DataCollectionRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DataCollectionRecord
 */
class DataCollectionRecordResource extends JsonResource
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
            'id'                 => $this->getKey(),
            'product_id'         => $this->product_id,
            'user_id'            => $this->user_id,
            'quantity_collected' => $this->quantity_collected,
            'quantity_expected'  => $this->quantity_expected,
            'quantity_required'  => $this->quantity_required,
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,

            'product' => ProductResource::make($this->product),
            'user'    => UserResource::make($this->user),
        ];
    }
}
