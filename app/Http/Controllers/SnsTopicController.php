<?php

namespace App\Http\Controllers;

use App\User;
use Aws\Exception\AwsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SnsTopicController extends Controller
{
    private $awsSnsClient;
    private $topicName;

    /**
     * SnsTopicController constructor.
     * @param $topic_prefix
     */
    public function __construct($topic_prefix)
    {
        $this->awsSnsClient = \AWS::createClient('sns');
        $this->topicName = $topic_prefix;
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
            Log::critical("Could not create SNS topic", ["code" => $e->getStatusCode(), "message" => $e->getMessage()]);
            return false;
        }

        return true;
    }

    /**
     * @param string $subscription_url
     * @return bool
     */
    public function subscribeToTopic(string $subscription_url) {
        try {
            $this->awsSnsClient->subscribe([
                'Protocol' => 'https',
                'Endpoint' => $subscription_url,
                'ReturnSubscriptionArn' => true,
                'TopicArn' => $this->getTopicArn(),
            ]);

        } catch (AwsException $e) {
            Log::critical("Could not subscribe to SNS topic", ["code" => $e->getStatusCode(), "message" => $e->getMessage()]);
            return false;
        }

        return true;
    }

    /**
     * @param string $message
     * @return bool
     */
    function publish(string $message){

        logger("Publishing SNS message", ["message" => $message]);

        $notification = [
            'Message'   => $message,
            'TargetArn' => $this->getTopicArn()
        ];

        try {

            $result = $this->awsSnsClient->publish($notification);

            logger("SNS message published", [
                "Message" => $notification,
                "MessageId" => $result["MessageId"],
                "Result" => $result["@metadata"]["statusCode"]
            ]);

            return true;

        } catch (AwsException $e) {
            switch ($e->getStatusCode())
            {
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
                    throw $e;
            }

        }


    }

    /**
     * @return bool
     */
    public function deleteTopic() {

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
    private function getFullTopicName(): string
    {
        return implode('', [
            config('app.sns_topic_prefix', ''),
            $this->topicName
        ]);
    }

    /**
     * @return string
     */
    private function getTopicArn(): string
    {
        return implode(":",[
            "arn",
            "aws",
            "sns",
            config('aws.region'),
            config('aws.user_code'),
            $this->getFullTopicName()
        ]);
    }
}
