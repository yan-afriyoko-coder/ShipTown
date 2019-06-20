<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class productController extends snsController
{
    //protected $topicName='product';
    protected $topicName = "snsProductsPostTopic";

    function validation() {
        return true;
    }
}
