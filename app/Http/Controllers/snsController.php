<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AWS;
use Auth;

class snsController extends Controller
{
    protected $topicName = "";

    function store(Request $request){
        $message = $request->getContent();

        if(!$this->validation($message)){
            return response()->json("Error 422: Invalid data", 422);
        }

        $this->sendTo($this->getTopicName(), $message);
    }

    function sendTo($topicName, $message){

        $aws = AWS::createClient('sns');

        $aws->publish([
            'Message'   => $message,
            'TargetArn' => $topicName
        ]);
    }

    function getTopicName(){

        $userID = auth('api')->user()->id;

        if(!isset($userID)){
            return response()->json("Error 403: Forbidden. Please log in", 403);

        }

        return "arn:aws:sns:".env('AWS_REGION').":".env('AWS_USER_CODE').":snsTopic_".$this->topicName."_User".$userID;
    }

    function validation($message){
        //
    }
}
