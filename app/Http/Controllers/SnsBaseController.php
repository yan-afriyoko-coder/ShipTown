<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AWS;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;

class SnsBaseController extends Controller
{
    private $awsSnsClient;
    protected $topicNamePrefix = "";

    public function __construct()
    {
        $this->awsSnsClient = AWS::createClient('sns');
    }

    function publishMessage($message){

        try {
        $result = $this->awsSnsClient->publish([
            'Message'   => $message,
            'TargetArn' => $this->getTargetArn()
        ]);

        } catch (AwsException $awsException) {

        }
        return $result['MessageId'];
    }

    function getUserSpecificTopicName($prefix){

        $userID = auth('api')->user()->id;

        return $prefix."_user".$userID;
    }

    public function getTargetArn($prefix)
    {
        return "arn:aws:sns:".env('AWS_REGION').":".env('AWS_USER_CODE').":".$this->getUserSpecificTopicName($prefix);
    }

    public function createTopic($prefix) {

        try {
            $this->awsSnsClient->createTopic([
                'Name' => $this->getUserSpecificTopicName($prefix)
            ]);

        } catch (AwsException $e) {
            Log::critical("Could not create SNS topic", ["code" => $e->getStatusCode(), "message" => $e->getMessage()]);
            return false;
        }

        return true;
    }

    public function subscribeToTopic(Request $request) {
        $subscribeUrl = $request->getContent();

        try {
            $result = $this->awsSnsClient->subscribe([
                'Protocol' => 'https',
                'Endpoint' => $subscribeUrl,
                'ReturnSubscriptionArn' => true,
                'TopicArn' => $this->getTargetArn(),
            ]);

        } catch (AwsException $e) {
            return response()->json("AWS error: ".$e);

        }

        return response()->json("Successfully subscribed '".$subscribeUrl."' to products topic", 200);
    }

    function validation($message){
        return true;
    }
}
