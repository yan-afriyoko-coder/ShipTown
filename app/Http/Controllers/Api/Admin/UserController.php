<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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
     * PUT api/admin/users.
     *
     * @param UserStoreRequest $request
     *
     * @return UserResource
     * @throws ValidationException
     */
    public function store(UserStoreRequest $request): UserResource
    {
        $user = User::where('email', $request->email)->onlyTrashed()->first();
        if ($user) {
            $user->restore();
            $user->update($request->validated());
        } else {
            $this->validate($request, [
                'email' => 'unique:users,email',
                'name'  => 'unique:users,name'
            ]);

            $user = User::create($request->validated() + ['password' => bcrypt(Str::random(8))]);
            Password::sendResetLink(
                $request->only('email')
            );
        }

        $user->assignRole($request->role_id);

        return new UserResource($user);
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
        } else {
            $user->syncRoles([$request->role_id]);
        }

        $user->fill($updateData->toArray());
        $user->save();

        return new UserResource($user);
    }

    /**
     * DELETE api/admin/users.
     *
     * @param UserDeleteRequest $request
     * @param int $user_id_to_delete
     * @return UserResource
     */
    public function destroy(UserDeleteRequest $request, int $user_id_to_delete): UserResource
    {
        abort_if($user_id_to_delete === $request->user()->getKey(), 403, 'You can not delete yourself');

        $user = User::query()->findOrFail($user_id_to_delete);

        $user->delete();

        return UserResource::make($user);
    }
}
