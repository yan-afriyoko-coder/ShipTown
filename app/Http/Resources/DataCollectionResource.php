<?php

namespace App\Http\Resources;

use App\Models\DataCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *  Class DataCollectionResource
 * @mixin DataCollection
 */
class DataCollectionResource extends JsonResource
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
            'id' => $this->id,
            'warehouse_id' => $this->warehouse_id,
            'destination_warehouse_id' => $this->destination_warehouse_id,
            'name' => $this->name,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'warehouse' => WarehouseResource::make($this->whenLoaded('warehouse')),
        ];
    }
}
