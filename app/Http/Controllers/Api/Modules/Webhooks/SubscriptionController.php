<?php

namespace App\Http\Controllers\Api\Modules\Webhooks;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebhookSubscriptionStoreRequest;
use App\Http\Resources\SubscriptionResource;
use App\Modules\Webhooks\src\Services\SnsService;
use Aws\Exception\AwsException;
use Aws\Result;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function index(): SubscriptionResource
    {
        $listSubscriptionsByTopicResponse = SnsService::client()
            ->listSubscriptionsByTopic([
                'TopicArn' => SnsService::getConfiguration()->topic_arn
            ]);

        return SubscriptionResource::make([
            "service" => 'AWS SNS',
            'method' => 'listSubscriptions',
            'response' => $listSubscriptionsByTopicResponse->toArray()
        ]);
    }

    public function store(WebhookSubscriptionStoreRequest $request): SubscriptionResource
    {
        $subscribeResponse = SnsService::subscribeOrFail($request->validated()['endpoint']);

        return SubscriptionResource::make([
            "service" => 'AWS SNS',
            'method' => 'subscribe',
            'response' => $subscribeResponse->toArray()
        ]);
    }
}
