<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserMeStoreRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

/**
 * Class UserMeController
 * @package App\Http\Controllers\Api\Settings\User
 */
class UserMeController extends Controller
{
    /**
     * @param Request $request
     * @return UserResource
     */
    public function index(Request $request)
    {
        return new UserResource($request->user());
    }

    /**
     * @param UserMeStoreRequest $request
     * @return UserResource
     */
    public function store(UserMeStoreRequest $request)
    {
        $request->user()->update($request->validated());

        return new UserResource($request->user());
    }
}
