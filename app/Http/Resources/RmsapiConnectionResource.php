<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RmsapiConnectionResource extends JsonResource
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
            'id' => $this->getKey(),
            'url' => $this->url,
            'location_id' => $this->location_id,
            'username' => $this->username,
            // Do not send password, even if it's encrypted
        ];
    }
}
