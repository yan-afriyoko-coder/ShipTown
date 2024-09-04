<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PreAdviceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray($request): array
    {
        return parent::toArray($request);
    }
}
