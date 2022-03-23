<?php

namespace App\Modules\Webhooks\src\Services;

use AWS;
use Aws\Exception\AwsException;
use Aws\Result;
use Aws\Sns\SnsClient;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class SnsController.
 */
class SnsService
{
    /**
     * @var SnsClient
     */
    public $awsSnsClient;

    /**
     * @var string
     */
    private string $topicName;

    /**
     * @var AwsException
     */
    public AwsException $lastException;

    /**
     * @var Result
     */
    public Result $lastResponse;

    /**
     * SnsController constructor.
     *
     * @param $topicName
     */
    public function __construct($topicName)
    {
        if (empty(config('aws.user_code')) === false) {
            $this->awsSnsClient = AWS::createClient('sns');
        }

        $this->topicName = $topicName;
    }

    /**
     * @return mixed
     */
    public function listSubscriptions()
    {
        return $this->awsSnsClient->listSubscriptions();
    }

    /**
     * @return bool
     */
    public function createTopic(): bool
    {
        try {
            $this->awsSnsClient->createTopic([
                'Name' => $this->getFullTopicName(),
            ]);
        } catch (AwsException $e) {
            $this->lastException = $e;
            Log::critical('Could not create SNS topic', ['code' => $e->getStatusCode(), 'message' => $e->getMessage()]);

            return false;
        }

        return true;
    }

    /**
     * @param string $subscription_url
     *
     * @return bool
     */
    public function subscribeToTopic(string $subscription_url): bool
    {
        try {
            $this->awsSnsClient->subscribe([
                'Protocol'              => 'https',
                'Endpoint'              => trim($subscription_url),
                'ReturnSubscriptionArn' => true,
                'TopicArn'              => $this->getTopicArn(),
            ]);
        } catch (AwsException $e) {
            Log::critical(
                'Could not subscribe to SNS topic',
                ['code' => $e->getStatusCode(), 'message' => $e->getMessage()]
            );

            return false;
        }

        return true;
    }

    /**
     * @param string $message
     *
     * @return bool
     */
    public function publish(string $message): bool
    {
        if (is_null($this->awsSnsClient)) {
            return false;
        }

        $notification = [
            'TargetArn' => $this->getTopicArn(),
            'Message'   => $message,
        ];

        try {
            $this->lastResponse = $this->awsSnsClient->publish($notification);
            Log::debug('AwsSns: message published', $this->lastResponse->toArray());

            return true;
        } catch (AwsException $e) {
            Log::error('Could not publish SNS message', [
                'code'           => $e->getStatusCode(),
                'return_message' => $e->getMessage(),
                'topic'          => $this->getTopicArn(),
                'message'        => $notification,
            ]);

            return false;
        } catch (Exception $e) {
            Log::error('Could not publish SNS message', [
                'code'           => $e->getCode(),
                'return_message' => $e->getMessage(),
                'topic'          => $this->getTopicArn(),
                'message'        => $notification,
            ]);

            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteTopic(): bool
    {
        try {
            $this->awsSnsClient->deleteTopic([
                'TopicArn' => $this->getTopicArn(),
            ]);
        } catch (AwsException $e) {
            Log::critical('Could not delete SNS topic', ['code' => $e->getStatusCode(), 'message' => $e->getMessage()]);

            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getFullTopicName(): string
    {
        return config('sns.topic.prefix', '') . $this->topicName;
    }

    /**
     * @return string
     */
    private function getTopicArn(): string
    {
        return implode(':', [
            'arn',
            'aws',
            'sns',
            config('aws.region'),
            config('aws.user_code'),
            $this->getFullTopicName(),
        ]);
    }
}
