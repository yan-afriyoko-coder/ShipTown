<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invites\UserInviteProcessRequest;
use App\Http\Requests\Invites\UserInviteStoreRequest;
use App\Mail\InviteCreated;
use App\Models\UserInvite;
use App\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class UserInviteController extends Controller
{
    /**
     * POST api/admin/user/invites
     *
     * @param UserInviteStoreRequest $request
     * @return void
     */
    public function store(UserInviteStoreRequest $request)
    {
        do {
            //generate a random string using Laravel's str_random helper
            $token = Str::random();
        } while (UserInvite::where('token', $token)->first());
        //check if the token already exists and if it does, try again

        //create a new invite record
        $invite = UserInvite::create([
            'email' => $request->get('email'),
            'token' => $token
        ]);

        // send the email
        Mail::to($request->get('email'))->queue(new InviteCreated($invite));

        $this->respondOK200('User invite created');
    }

    /**
     * GET invites/{invite_token}
     *
     * @param $token
     * @return array|Application|ResponseFactory|Factory|Response|View|mixed
     */
    public function accept($token)
    {
        // Look up the invite
        if (!$invite = UserInvite::where('token', $token)->first()) {
            return response('Not Found', 404);
        }

        return view('invites.accept', $invite);
    }

    /**
     * POST invites/{invite_token}
     *
     * @param UserInviteProcessRequest $request
     * @return Application|ResponseFactory|RedirectResponse|Response|Redirector|void
     */
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
