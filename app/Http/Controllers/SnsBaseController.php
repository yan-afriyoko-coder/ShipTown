<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AWS;
use Log;
use Aws\Exception\AwsException;

class SnsBaseController extends Controller
{
    private $awsSnsClient;
    protected $topicName = "";

    public function __construct()
    {
        $this->awsSnsClient = AWS::createClient('sns');
    }

    function store(Request $request){

        $message = $request->getContent();

        if(!$this->validation($message)){

            Log::warning('Invalid message: '.$message);
            return response()->json("Error 422: Invalid data", 422);

        }

        if ($this->sendTo($this->getTopicName(), $message)) {

            return response()->json("ok", 200);

        };

    }

    function sendTo($topicName, $message){

        $result = $this->awsSnsClient->publish([
            'Message'   => $message,
            'TargetArn' => $topicName
        ]);

        return $result['MessageId'];
    }

    function getTopicName(){

        $userID = auth('api')->user()->id;

        return "arn:aws:sns:".env('AWS_REGION').":".env('AWS_USER_CODE').":snsTopic_".$this->topicName."_User".$userID;
    }

    function create(Request $request) {


        $topic = $request->getContent();

        $userID = auth('api')->user()->id;

        try {

            $this->awsSnsClient->createTopic([

                'Name' => "snsTopic_".$topic."_User".$userID,

            ]);

        } catch (AwsException $e) {

            Log::alert("AWS error: ".$e);
            return response()->json("AWS error: ".$e);

        }

        return response()->json("Successfully created topic '".$topic."' for user ".$userID, 200);
    }

    function subscribe(Request $request) {


        $url = $request->getContent();

        $userID = auth('api')->user()->id;

        $protocol = 'https';

        $endpoint = $url;

        $topic = "arn:aws:sns:".env('AWS_REGION').":".env('AWS_USER_CODE').":snsTopic_Products_User".$userID;



        try {

            $result = $this->awsSnsClient->subscribe([

                'Protocol' => $protocol,
                'Endpoint' => $endpoint,
                'ReturnSubscriptionArn' => true,
                'TopicArn' => $topic,

            ]);

        } catch (AwsException $e) {

            return response()->json("AWS error: ".$e);

        }

        return response()->json("Successfully subscribed '".$url."' to products topic", 200);

    }

    function validation($message){
        return true;
    }
}
