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

    public function create_user_topic() {

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

    public function subscribe_to_user_topic($subscription_url) {
        try {
            $this->_awsSnsClient->subscribe([
                'Protocol' => 'https',
                'Endpoint' => $subscription_url,
                'ReturnSubscriptionArn' => true,
                'TopicArn' => $this->getTargetArn(),
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

        try {
            $message = [
                'Message'   => $message,
                'TargetArn' => $this->getTargetArn()
            ];

            info("Publishing SNS message", $message);

            $result = $this->_awsSnsClient->publish($message);

            logger("SNS message published", [
                "Message" => $message,
                "TargetArn" => $this->getTargetArn(),
                "MessageId" => $result["MessageId"],
                "Result" => $result["@metadata"]["statusCode"]
            ]);

            return true;

        } catch (AwsException $e) {
            switch ($e->getStatusCode())
            {
                case 404:
                    $this->create_user_topic();
                    $this->publish_message($message);
                    break;
                default:
                    Log::error("Could not publish SNS message", [
                        "code" => $e->getStatusCode(),
                        "return_message" => $e->getMessage(),
                        "message" => $message
                    ]);
                    throw $e;
            }

        }


    }

    public function delete_user_topic() {

        try {
            $this->_awsSnsClient->deleteTopic([
                'TopicArn' => $this->getTargetArn()
            ]);

        } catch (AwsException $e) {
            Log::critical("Could not delete SNS topic", ["code" => $e->getStatusCode(), "message" => $e->getMessage()]);
            return false;
        }

        return true;
    }

    private function getTopicName(): string
    {
        if(config('use_subdomain_prefixed_topic_name')) {
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
    private function getTargetArn(): string
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
