<?php

namespace App\Http\Controllers;

class OrdersController extends SnsBaseController
{
    protected $topicNamePrefix = "Orders";

    function validation($message) {

        if(($message == "") or (!isset($message)) or ($message == '[""]')){

            return false;

        }

        return true;
    }
}
