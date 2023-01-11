<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\TwoFactorControllerIndexRequest;
use App\Http\Requests\TwoFactorStoreRequest;
use App\Notifications\TwoFactorCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Guid\Guid;

class TwoFactorController extends Controller
{
    private int $lifetimeInMinutes = 60 * 24 * 7;

    public function index(TwoFactorControllerIndexRequest $request): mixed
    {
        if (config('two_factor_auth.disabled')) {
            return redirect()->home();
        }

        if ($request->cookie('device_guid') !== null) {
            return redirect()->home();
        }

        $user = $request->user();

        if ($user->two_factor_expires_at && $user->two_factor_expires_at->isPast()) {
            Auth::logoutCurrentDevice();
            $user->resetTwoFactorCode();
            return redirect()->route('login')->withErrors(['two_factor_code' => 'Invalid code']);
        }

        if ($request->has('two_factor_code')) {
            if ($user->two_factor_code === $request->input('two_factor_code')) {
                $user->resetTwoFactorCode();
                return redirect()->home()->withCookie(cookie('device_guid', Guid::uuid4(), $this->lifetimeInMinutes));
            }

            if ($user->two_factor_code !== $request->input('two_factor_code')) {
                Auth::logoutCurrentDevice();
                $user->resetTwoFactorCode();
                return redirect()->route('login')->withErrors(['two_factor_code' => 'Invalid code']);
            }
        }

        if ($user->two_factor_code === null) {
            $user->generateTwoFactorCode();
            $user->notify(new TwoFactorCode());
        }

        return view('auth.twoFactor');
    }

    public function store(TwoFactorStoreRequest $request): RedirectResponse
    {
        if ($request->input('two_factor_code') !== $request->user()->two_factor_code) {
            Auth::logoutCurrentDevice();
            $request->user()->resetTwoFactorCode();
            return redirect()->route('login')->withErrors(['two_factor_code' => 'Invalid code']);
        }

        $request->user()->resetTwoFactorCode();

        return redirect()->home()->withCookie(cookie('device_guid', Guid::uuid4(), $this->lifetimeInMinutes));
    }
}
