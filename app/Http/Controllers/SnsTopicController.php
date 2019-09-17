<?php

namespace App\Http\Controllers;

use Aws\Exception\AwsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SnsTopicController extends Controller
{
    private $awsSnsClient;
    private $_topicPrefix;

    public function __construct($topic_prefix)
    {
        $this->awsSnsClient = \AWS::createClient('sns');
        $this->_topicPrefix = $topic_prefix;
    }

    public function create_user_topic($prefix) {

        try {
            $this->awsSnsClient->createTopic([
                'Name' => $this->get_user_specific_topic_name($prefix)
            ]);

        } catch (AwsException $e) {
            Log::critical("Could not create SNS topic", ["code" => $e->getStatusCode(), "message" => $e->getMessage()]);
            return false;
        }

        return true;
    }

    public function subscribe_to_user_topic($topic_prefix, $subscription_url) {
        try {
            $this->awsSnsClient->subscribe([
                'Protocol' => 'https',
                'Endpoint' => $subscription_url,
                'ReturnSubscriptionArn' => true,
                'TopicArn' => $this->get_user_specific_topic_arn($topic_prefix),
            ]);

        } catch (AwsException $e) {
            Log::critical("Could not subscribe to SNS topic", ["code" => $e->getStatusCode(), "message" => $e->getMessage()]);
            return false;
        }

        return true;
    }

    public function delete_user_topic($prefix) {

        try {
            $this->awsSnsClient->deleteTopic([
                'TopicArn' => $this->get_user_specific_topic_arn($prefix)
            ]);

        } catch (AwsException $e) {
            Log::critical("Could not delete SNS topic", ["code" => $e->getStatusCode(), "message" => $e->getMessage()]);
            return false;
        }

        return true;
    }

    private function get_user_specific_topic_name($prefix): string
    {
        $userID = auth('api')->user()->id;

        return $prefix."_user".$userID;
    }

   private function get_user_specific_topic_arn($prefix): string
    {
        $arn = "arn:aws:sns:".env('AWS_REGION').":".env('AWS_USER_CODE');

        return $arn.":".$this->get_user_specific_topic_name($prefix);
    }
}
