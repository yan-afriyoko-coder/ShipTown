<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AWS;

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
        return "arn:aws:sns:eu-west-1:310005059065:".auth('api')->user()->id."_sns".$this->topicName."PostTopic";

    }

    function validation($message){
        //
    }
}
