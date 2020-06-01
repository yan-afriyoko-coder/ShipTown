<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\Users\UpdateRequest;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return UserResource::collection(User::all());
        } else {

        }
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(UpdateRequest $request, User $user)
    {
        $user->update(['name' => $request->name]);
        $user->save();
        // Allow changing of role if the current user has permissions and not editing self.
        if ($request->user()->can('manage users') && $request->user()->id != $user->id) {
            $user->syncRoles($request->role_id);
        }

        return new UserResource($user);
    }
}
