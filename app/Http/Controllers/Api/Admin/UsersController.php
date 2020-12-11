<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\DeleteRequest;
use App\Http\Requests\Users\UpdateRequest;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        return UserResource::collection(User::all());
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function me(Request $request)
    {
        return $this->show($request->user());
    }

    public function update(UpdateRequest $request, User $user)
    {
        $user->name = $request->name;

        if ($request->has('printer_id')) {
            $user->printer_id = $request->printer_id;
        }

        $user->save();
        // Allow changing of role if the current user has permissions and not editing self.
        if ($request->user()->can('manage users') && $request->user()->id != $user->id) {
            $user->syncRoles($request->role_id);
        }

        return new UserResource($user);
    }

    public function destroy(DeleteRequest $request, User $user)
    {
        $user->delete();
        return $this->respondOK200('ok');
    }
}
