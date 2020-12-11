<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\DeleteRequest;
use App\Http\Requests\Users\UpdateRequest;
use App\Http\Resources\UserResource;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class UsersController
 * @package App\Http\Controllers\Api\Admin
 */
class UsersController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = User::getSpatieQueryBuilder();

        return UserResource::collection($this->getPaginatedResult($query));
    }

    /**
     * @param User $user
     * @return UserResource
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * @param UpdateRequest $request
     * @param User $user
     * @return UserResource
     */
    public function update(UpdateRequest $request, User $user)
    {
        $updateData = collect($request->validated());

        // Not allowed to update your own role
        if ($request->user()->id === $user->id) {
            $updateData->forget('role_id');
        }

        $user->fill($updateData->toArray());
        $user->save();

        return new UserResource($user);
    }

    /**
     * @param DeleteRequest $request
     * @param User $user
     * @return UserResource
     * @throws Exception
     */
    public function destroy(DeleteRequest $request, User $user)
    {
        $user->delete();
        return new UserResource($user);
    }
}
