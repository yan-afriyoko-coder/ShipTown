<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductsController extends snsController
{
    protected $topicName = "Products";

    function validation($message) {

        if(($message == "") or (!isset($message)) or ($message == '[""]')){
            return false;
        }
        return true;
    }
}
