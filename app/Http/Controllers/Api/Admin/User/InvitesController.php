<?php

namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invites\ProcessRequest;
use App\Http\Requests\Invites\StoreRequest;
use App\Mail\InviteCreated;
use App\Models\Invite;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class InvitesController extends Controller
{
    public function store(StoreRequest $request)
    {
        do {
            //generate a random string using Laravel's str_random helper
            $token = str_random();
        } while (Invite::where('token', $token)->first()); //check if the token already exists and if it does, try again

        //create a new invite record
        $invite = Invite::create([
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
        if (!$invite = Invite::where('token', $token)->first()) {
            return response('Not Found', 404);
        }

        return view('invites.accept', $invite);
    }

    public function process(ProcessRequest $request)
    {
        // Look up the invite
        if (!$invite = Invite::where('token', $request->input('token'))->first()) {
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
