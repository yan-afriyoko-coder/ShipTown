<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
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
            'log_name' => $this->log_name,
            'description' => $this->description,
            'subject_id' => $this->subject_id,
            'subject_type' => $this->subject_type,
            'causer_id' => $this->causer_id,
            'causer_type' => $this->causer_type,
            'properties' => $this->properties,
            'causer' => new UserResource($this->whenLoaded('causer')),
            'changes' => isset($this->properties['old'])
                ? array_diff($this->properties['attributes'], $this->properties['old'])
                : (isset($this->properties['attributes']) ? $this->properties['attributes'] : []),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
