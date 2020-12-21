<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invites\UserInviteProcessRequest;
use App\Http\Requests\Invites\UserInviteStoreRequest;
use App\Mail\InviteCreated;
use App\Models\UserInvite;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserInviteController extends Controller
{
    public function store(UserInviteStoreRequest $request)
    {
        do {
            //generate a random string using Laravel's str_random helper
            $token = str_random();
        } while (UserInvite::where('token', $token)->first()); //check if the token already exists and if it does, try again

        //create a new invite record
        $invite = UserInvite::create([
            'email' => $request->get('email'),
            'token' => $token
        ]);

        // send the email
        Mail::to($request->get('email'))->queue(new InviteCreated($invite));

        return response('ok', 201);
    }

    public function accept($token)
    {
        // Look up the invite
        if (!$invite = UserInvite::where('token', $token)->first()) {
            return response('Not Found', 404);
        }

        return view('invites.accept', $invite);
    }

    public function process(UserInviteProcessRequest $request)
    {
        // Look up the invite
        if (!$invite = UserInvite::where('token', $request->input('token'))->first()) {
            return response('Not Found', 404);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $invite->email,
            'password' => Hash::make($request->input('password')),
        ]);

        $user->assignRole('user');

        $invite->delete();

        Auth::guard()->login($user);

        return redirect('/dashboard');
    }
}
