<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class productController extends snsController
{
    protected $topicName = "Products";

    function validation() {
        return true;
    }
}
