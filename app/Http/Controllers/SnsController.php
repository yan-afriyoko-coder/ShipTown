<?php

namespace App\Http\Controllers;

use Aws\Exception\AwsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SnsController extends Controller
{
    private $awsSnsClient;

    public function __construct()
    {
        $this->awsSnsClient = \AWS::createClient('sns');
    }

    public function createTopic($prefix) {

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

    private function get_user_specific_topic_name($prefix): string
    {
        $userID = auth('api')->user()->id;

        return $prefix."_user".$userID;
    }

   private function getUserSpecificArn($prefix): string
    {
        $arn = "arn:aws:sns:".env('AWS_REGION').":".env('AWS_USER_CODE');

        return $arn.":".$this->get_user_specific_topic_name($prefix);
    }
}
