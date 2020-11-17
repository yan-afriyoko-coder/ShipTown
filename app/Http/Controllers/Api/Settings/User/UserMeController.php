<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserMeController extends Controller
{
    public function index(Request $request)
    {
        return new UserResource($request->user());
    }
}
