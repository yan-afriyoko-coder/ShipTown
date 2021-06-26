<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\User;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class UsersController.
 */
class UserController extends Controller
{
    /**
     * GET api/admin/users.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $query = User::getSpatieQueryBuilder();

        return UserResource::collection($this->getPaginatedResult($query));
    }

    /**
     * SHOW api/admin/users.
     *
     * @param User $user
     *
     * @return UserResource
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * PUT api/admin/users.
     *
     * @param UserUpdateRequest $request
     * @param User              $user
     *
     * @return UserResource
     */
    public function update(UserUpdateRequest $request, User $user): UserResource
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
     * DELETE api/admin/users.
     *
     * @param UserDeleteRequest $request
     * @param User              $user
     *
     * @throws Exception
     *
     * @return UserResource
     */
    public function destroy(UserDeleteRequest $request, User $user)
    {
        $user->delete();

        return new UserResource($user);
    }
}
