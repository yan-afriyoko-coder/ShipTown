<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderCommentResource extends JsonResource
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
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'comment' => $this->comment,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
