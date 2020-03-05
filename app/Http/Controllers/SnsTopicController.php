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
                'Name' => $this->get_user_specific_topic_name()
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
                'TopicArn' => $this->get_user_specific_topic_arn(),
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
            $result = $this->_awsSnsClient->publish([
                'Message'   => $message,
                'TargetArn' => $this->get_user_specific_topic_arn()
            ]);

            $content = [
                "Message" => $message,
                "TargetArn" => $this->get_user_specific_topic_arn(),
                "MessageId" => $result["MessageId"],
                "Result" => $result["@metadata"]["statusCode"]
            ];

            info("SNS message published", $content);

            return true;

        } catch (AwsException $e) {
            switch ($e->getStatusCode())
            {
                case 404:
                    $this->create_user_topic();
                    $this->publish_message($message);
                    break;
                default:
                    Log::critical("Could not publish message", ["code" => $e->getStatusCode(), "message" => $e->getMessage()]);
                    throw $e;
            }

        }


    }

    public function delete_user_topic() {

        try {
            $this->_awsSnsClient->deleteTopic([
                'TopicArn' => $this->get_user_specific_topic_arn()
            ]);

        } catch (AwsException $e) {
            Log::critical("Could not delete SNS topic", ["code" => $e->getStatusCode(), "message" => $e->getMessage()]);
            return false;
        }

        return true;
    }

    private function get_user_specific_topic_name(): string
    {
        $userID = auth()->user()->id;

        return $this->_topicPrefix."_user".$userID;
    }

   private function get_user_specific_topic_arn(): string
    {
        $arn = "arn:aws:sns:".env('AWS_REGION').":".env('AWS_USER_CODE');

        return $arn.":".$this->get_user_specific_topic_name();
    }
}
