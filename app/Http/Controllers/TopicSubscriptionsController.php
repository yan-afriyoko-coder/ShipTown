<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TopicSubscriptionsController extends Controller
{
    public function store(Request $request){
        $this->respond_OK_200();
    }
}
