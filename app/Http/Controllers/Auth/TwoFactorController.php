<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\TwoFactorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($request->input('two_factor_code') == $user->two_factor_code) {
            $user->resetTwoFactorCode();

            return redirect()->route('dashboard');
        }

        if ($request->input('two_factor_code')) {
            Auth::logout();
            return redirect()->home();
        }

        return view('auth.twoFactor');
    }

    public function store(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'integer|required',
        ]);

        $user = auth()->user();

        if ($request->input('two_factor_code') == $user->two_factor_code) {
            $user->resetTwoFactorCode();
            return redirect()->route('dashboard');
        }

        if ($request->input('two_factor_code')) {
            Auth::logout();
            return redirect()->route('login');
        }

        return redirect()->back()
            ->withErrors(['two_factor_code' =>
                'The two factor code you have entered does not match']);
    }

    public function resend()
    {
        $user = auth()->user();
        $user->generateTwoFactorCode();
        $user->notify(new TwoFactorCode());

        return redirect()->back()->withMessage('The two factor code has been sent again');
    }
}
