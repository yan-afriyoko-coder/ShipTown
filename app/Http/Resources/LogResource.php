<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class LogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'created_at'   => $this->created_at,
            'id'           => $this->id,
            'description'  => $this->description,
            'subject_id'   => $this->subject_id,
            'subject_type' => $this->subject_type,
            'causer_id'    => $this->causer_id,
            'causer_type'  => $this->causer_type,
            'properties'   => $this->properties,
            'changes'      => $this->getChanges(),
            'causer'       => $this->whenLoaded('causer'),
        ];
    }

    /**
     * @return array
     */
    private function getChanges(): array
    {
        $result = [];

        if (Arr::has($this->properties, ['old'])) {
            $array_keys = array_keys($this->properties['attributes']);

            foreach ($array_keys as $key) {
                if ($this->properties['attributes'][$key] !== $this->properties['old'][$key]) {
                    $result[$key] = $this->properties['attributes'][$key];
                }
            }
        }

        return $result;
    }
}
