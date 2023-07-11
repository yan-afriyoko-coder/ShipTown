<?php

namespace App\Http\Controllers\Api\Modules\Slack;

use App\Http\Controllers\Controller;
use App\Modules\Slack\src\Http\Requests\IncomingWebhookStoreRequest;
use App\Modules\Slack\src\Http\Resources\IncomingWebhookResource;
use App\Modules\Slack\src\Models\IncomingWebhook;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;

class ConfigurationController extends Controller
{
    public function store(IncomingWebhookStoreRequest $request): AnonymousResourceCollection
    {
        $incomingWebhook = IncomingWebhook::query()->create($request->validated());

        return IncomingWebhookResource::collection(Arr::wrap($incomingWebhook));
    }
}
