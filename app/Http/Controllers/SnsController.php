<?php

namespace App\Http\Controllers;

use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;

class SnsController extends Controller
{
    private $awsSnsClient;
    private $topicName;


    /**
     * @var AwsException
     */
    public $lastException;

    /**
     * SnsController constructor.
     * @param $topicName
     */
    public function __construct($topicName)
    {
        if (empty(config('aws.user_code')) === false) {
            $this->awsSnsClient = \AWS::createClient('sns');
        };

        $this->topicName = $topicName;
    }

    /**
     * @return bool
     */
    public function createTopic()
    {
        try {
            $this->awsSnsClient->createTopic([
                'Name' => $this->getFullTopicName()
            ]);
        } catch (AwsException $e) {
            $this->lastException = $e;
            Log::critical("Could not create SNS topic", ["code" => $e->getStatusCode(), "message" => $e->getMessage()]);
            return false;
        }

        return true;
    }

    /**
     * @param string $subscription_url
     * @return bool
     */
    public function subscribeToTopic(string $subscription_url)
    {
        try {
            $this->awsSnsClient->subscribe([
                'Protocol' => 'https',
                'Endpoint' => $subscription_url,
                'ReturnSubscriptionArn' => true,
                'TopicArn' => $this->getTopicArn(),
            ]);
        } catch (AwsException $e) {
            Log::critical(
                "Could not subscribe to SNS topic",
                ["code" => $e->getStatusCode(), "message" => $e->getMessage()]
            );
            return false;
        }

        return true;
    }

    /**
     * @param string $message
     * @return bool
     */
    public function publish(string $message)
    {

        if (is_null($this->awsSnsClient)) {
            return false;
        }

        $notification = [
            'TargetArn' => $this->getTopicArn(),
            'Message'   => $message,
        ];

        logger("Publishing SNS message", $notification);

        try {
            $result = $this->awsSnsClient->publish($notification);

            logger("SNS message published", [
                "Message" => $notification,
                "MessageId" => $result["MessageId"],
                "Result" => $result["@metadata"]["statusCode"]
            ]);

            return true;
        } catch (AwsException $e) {
            switch ($e->getStatusCode()) {
                case 404:
                    $this->createTopic();
                    $this->publish($message);
                    break;
                default:
                    Log::error("Could not publish SNS message", [
                        "code" => $e->getStatusCode(),
                        "return_message" => $e->getMessage(),
                        "message" => $notification
                    ]);
            }
        } catch (\Exception $e) {
            Log::error("Could not publish SNS message", [
                "code" => $e->getCode(),
                "return_message" => $e->getMessage(),
                "message" => $notification
            ]);
        }
    }

    /**
     * @return bool
     */
    public function deleteTopic()
    {

        try {
            $this->awsSnsClient->deleteTopic([
                'TopicArn' => $this->getTopicArn()
            ]);
        } catch (AwsException $e) {
            Log::critical("Could not delete SNS topic", ["code" => $e->getStatusCode(), "message" => $e->getMessage()]);
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getFullTopicName(): string
    {
        return implode('', [
            config('sns.topic.prefix', ''),
            '_',
            $this->topicName
        ]);
    }

    /**
     * @return string
     */
    private function getTopicArn(): string
    {
        return implode(":", [
            "arn",
            "aws",
            "sns",
            config('aws.region'),
            config('aws.user_code'),
            $this->getFullTopicName()
        ]);
    }
}
