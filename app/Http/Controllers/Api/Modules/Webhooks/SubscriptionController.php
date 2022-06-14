<?php

namespace App\Http\Controllers\Api\Modules\Webhooks;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebhookSubscriptionStoreRequest;
use App\Http\Resources\SubscriptionResource;
use App\Modules\Webhooks\src\Services\SnsService;

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
        $subscribeResponse = SnsService::client()
            ->subscribe([
                'Protocol' => 'https',
                'Endpoint' => $request->validated()['endpoint'],
                'ReturnSubscriptionArn' => true,
                'TopicArn' => SnsService::getConfiguration()->topic_arn,
            ]);

        return SubscriptionResource::make([
            "service" => 'AWS SNS',
            'method' => 'subscribe',
            'response' => $subscribeResponse->toArray()
        ]);
    }
}
