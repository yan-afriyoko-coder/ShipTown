<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\TwoFactorStoreRequest;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->two_factor_code === null) {
            return redirect()->home();
        }

        if ($request->input('two_factor_code') === $user->two_factor_code) {
            $user->resetTwoFactorCode();
            return redirect()->home();
        }

        return view('auth.twoFactor');
    }

    public function store(TwoFactorStoreRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        if ($request->input('two_factor_code') == $user->two_factor_code) {
            $user->resetTwoFactorCode();
            return redirect()->home();
        }

        if ($request->input('two_factor_code')) {
            Auth::logout();
            return redirect()->home();
        }

        return redirect()->back()
            ->withErrors(['two_factor_code' => 'The two factor code you have entered does not match']);
    }
}
