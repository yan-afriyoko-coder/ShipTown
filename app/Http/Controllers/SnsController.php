<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SnsController extends Controller
{
    private $awsSnsClient;

    public function __construct()
    {
        $this->awsSnsClient = AWS::createClient('sns');
    }
}
