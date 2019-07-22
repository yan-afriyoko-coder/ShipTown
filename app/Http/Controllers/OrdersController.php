<?php

namespace App\Http\Controllers;

use Log;

class OrdersController extends SnsBaseController
{
    protected $topicName = "Orders";

    function validation($message) {

        if(($message == "") or (!isset($message)) or ($message == '[""]')){

            return false;

        }

        return true;
    }
}
