<?php

namespace App\Http\Controllers;

use App\User;
use Aws\Exception\AwsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SnsTopicController extends Controller
{
    private $_awsSnsClient;
    private $_topicPrefix;

    public function __construct($topic_prefix)
    {
        $this->_awsSnsClient = \AWS::createClient('sns');
        $this->_topicPrefix = $topic_prefix;
    }

    public function createTopic() {

        try {
            $this->_awsSnsClient->createTopic([
                'Name' => $this->getTopicName()
            ]);

        } catch (AwsException $e) {
            Log::critical("Could not create SNS topic", ["code" => $e->getStatusCode(), "message" => $e->getMessage()]);
            return false;
        }

        return true;
    }

    public function subscribeToTopic($subscription_url) {
        try {
            $this->_awsSnsClient->subscribe([
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
     * @param $message
     * @return bool
     */
    function publish_message($message){

        logger("Publishing SNS message", ["message" => $message]);

        $notification = [
            'Message'   => $message,
            'TargetArn' => $this->getTopicArn()
        ];

        try {

            $result = $this->_awsSnsClient->publish($notification);

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
                    $this->publish_message($message);
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

    public function deleteTopic() {

        try {
            $this->_awsSnsClient->deleteTopic([
                'TopicArn' => $this->getTopicArn()
            ]);

        } catch (AwsException $e) {
            Log::critical("Could not delete SNS topic", ["code" => $e->getStatusCode(), "message" => $e->getMessage()]);
            return false;
        }

        return true;
    }

    private function getTopicName(): string
    {
        if(config('app.use_subdomain_prefixed_topic_name')) {
            return implode('_',[
                env('DB_TABLE_PREFIX',''),
                $this->_topicPrefix
            ]);
        }

        $userID = auth()->user()->id;

        return $this->_topicPrefix."_user".$userID;
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
            $this->getTopicName()
        ]);
    }
}
