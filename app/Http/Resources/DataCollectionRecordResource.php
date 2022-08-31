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
            'id'                 => $this->id,
            'product_id'         => $this->product_id,
            'quantity_requested' => $this->quantity_requested,
            'quantity_scanned'   => $this->quantity_scanned,
            'quantity_to_scan'   => $this->quantity_to_scan,
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,

            'product' => ProductResource::make($this->product),
        ];
    }
}
