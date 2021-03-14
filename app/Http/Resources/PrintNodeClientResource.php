<?php

namespace App\Http\Resources;

use App\Modules\PrintNode\src\PrintNode;
use Illuminate\Http\Resources\Json\JsonResource;

class PrintNodeClientResource extends JsonResource
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
            'api_key' => '****',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
