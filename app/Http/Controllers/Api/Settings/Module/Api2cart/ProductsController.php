<?php

namespace App\Http\Controllers\Api\Settings\Module\Api2cart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        return $this->respondOK200();
    }
}
