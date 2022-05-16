<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DpdUkConnectionResource extends JsonResource
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
            'id' => $this->id,
            'account_number' => $this->account_number,
            'username' => $this->username,
            'password' => '***',
            'collection_address_id' => $this->collection_address_id,
            'geo_session' => $this->geo_session,
            'collection_address' => OrderAddressResource::make($this->collectionAddress()->first()),
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ];
    }
}
