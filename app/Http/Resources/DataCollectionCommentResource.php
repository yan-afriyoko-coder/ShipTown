<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DataCollectionCommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data_collection_id' => $this->data_collection_id,
            'user_id'  => $this->user_id,
            'comment'  => $this->comment,
            'user'     => new UserResource($this->whenLoaded('user')),
        ];
    }
}
