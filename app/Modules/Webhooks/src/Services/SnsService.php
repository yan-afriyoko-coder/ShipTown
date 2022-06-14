<?php

namespace App\Modules\Webhooks\src\Services;

use App\Modules\Webhooks\src\Models\WebhooksConfiguration;
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

    public static function client()
    {
        return (new SnsService(''))->awsSnsClient;
    }
    /**
     * SnsController constructor.
     *
     * @param string $topicName
     */
    public function __construct(string $topicName = '')
    {
        $this->awsSnsClient = AWS::createClient('sns');

        $this->topicName = $topicName;
    }

    /**
     * @return WebhooksConfiguration
     */
    public static function getConfiguration(): WebhooksConfiguration
    {
        /** @var WebhooksConfiguration $model */
        $model = WebhooksConfiguration::query()->firstOrCreate([], []);

        return $model;
    }

    /**
     * @return mixed
     */
    public function listSubscriptions()
    {
        return $this->awsSnsClient->listSubscriptions();
    }

    /**
     * @param string|null $topic_name
     * @return bool
     */
    public function createTopic(string $topic_name = null): bool
    {
        if ($topic_name === null) {
            $topic_name = $this->getFullTopicName();
        }

        try {
            $this->awsSnsClient->createTopic(['Name' => $topic_name,]);
        } catch (AwsException $e) {
            $this->lastException = $e;
            Log::critical('Could not create SNS topic', ['code' => $e->getStatusCode(), 'message' => $e->getMessage()]);

            return false;
        }

        return true;
    }

    /**
     * @param string $subscription_url
     * @return bool
     */
    public static function subscribeToTopic(string $subscription_url): bool
    {
        self::client()->subscribe([
            'Protocol'              => 'https',
            'Endpoint'              => trim($subscription_url),
            'ReturnSubscriptionArn' => true,
            'TopicArn'              => self::getConfiguration()->topic_arn,
        ]);

        return true;
    }

    /**
     * @param string $message
     * @param null $topic_name
     * @return bool
     */
    public function publish(string $message, $topic_name = null): bool
    {
        if (is_null($this->awsSnsClient)) {
            return false;
        }

        if ($topic_name === null) {
            $topic_name = self::getFullTopicName($this->topicName);
        }

        $notification = [
            'TargetArn' => self::getTopicArn($topic_name),
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
                'topic'          => self::getTopicArn($topic_name),
                'message'        => $notification,
            ]);

            return false;
        } catch (Exception $e) {
            Log::error('Could not publish SNS message', [
                'code'           => $e->getCode(),
                'return_message' => $e->getMessage(),
                'topic'          => self::getTopicArn($topic_name),
                'message'        => $notification,
            ]);

            return false;
        }
    }

    /**
     * @param string $message
     * @param string $event_type
     * @return Result
     */
    public static function publishNew(string $message, string $event_type): Result
    {
        $notification = [
            'TargetArn' => self::getConfiguration()->topic_arn,
            'Message'   => $message,
            'MessageAttributes' => [
                'EventType' => array(
                    'DataType' => 'String',
                    'StringValue' => $event_type,
                ),
            ],
        ];

        return self::client()->publish($notification);
    }

    /**
     * @param null $topic_name
     * @return bool
     */
    public function deleteTopic($topic_name = null): bool
    {
        try {
            $this->awsSnsClient->deleteTopic([
                'TopicArn' => self::getTopicArn($topic_name),
            ]);
        } catch (AwsException $e) {
            Log::critical('Could not delete SNS topic', ['code' => $e->getStatusCode(), 'message' => $e->getMessage()]);

            return false;
        }

        return true;
    }

    /**
     * @param string $topic_name
     * @return string
     */
    public static function getFullTopicName(string $topic_name): string
    {
        return config('sns.topic.prefix', '') . $topic_name;
    }

    /**
     * @param string|null $topic_name
     * @return string
     */
    public static function getTopicArn(string $topic_name = null): string
    {
        return implode(':', [
            'arn',
            'aws',
            'sns',
            config('aws.region'),
            config('aws.user_code'),
            $topic_name,
        ]);
    }
}
