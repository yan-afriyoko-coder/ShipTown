<?php

namespace App\Http\Controllers\Api\Settings\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserMeController extends Controller
{
    public function index(Request $request)
    {
        return new UserResource($request->user());
    }
}
