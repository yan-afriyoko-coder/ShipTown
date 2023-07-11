<?php

namespace App\Modules\Slack\src\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class IncomingWebhookResource extends JsonResource
{
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'webhook_url' => $this->webhook_url,
        ];
    }
}
