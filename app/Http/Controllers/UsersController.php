<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Http\Resources\UserResource;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return UserResource::collection(User::all());
        } else {

        }
    }    
}
