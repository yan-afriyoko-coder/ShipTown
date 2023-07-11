<?php

namespace App\Http\Controllers\Api\Modules\Slack;

use App\Http\Controllers\Controller;
use App\Modules\Slack\src\Http\Requests\IncomingWebhookStoreRequest;
use App\Modules\Slack\src\Http\Resources\IncomingWebhookResource;
use App\Modules\Slack\src\Models\IncomingWebhook;

class ConfigurationController extends Controller
{
    public function store(IncomingWebhookStoreRequest $request): IncomingWebhookResource
    {
        $incomingWebhook = IncomingWebhook::query()->create($request->validated());

        return IncomingWebhookResource::make($incomingWebhook);
    }
}
