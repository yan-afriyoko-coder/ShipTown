<?php

namespace App\Http\Resources;

use App\Models\DataCollectionRecord;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DataCollectionRecord
 */
class DataCollectionRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
