<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\TwoFactorControllerIndexRequest;
use App\Http\Requests\TwoFactorStoreRequest;
use App\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function index(TwoFactorControllerIndexRequest $request): View|Factory|Application|RedirectResponse
    {
        if ($request->user()->two_factor_code === null) {
            return redirect()->home();
        }

        if ($request->input('two_factor_code') === $request->user()->two_factor_code) {
            $request->user()->resetTwoFactorCode();
            return redirect()->home();
        }

        return view('auth.twoFactor');
    }

    public function store(TwoFactorStoreRequest $request): RedirectResponse
    {
        if ($request->input('two_factor_code') !== $request->user()->two_factor_code) {
            Auth::logoutCurrentDevice();
            return redirect()->route('login')->withErrors(['two_factor_code' => 'Invalid code']);
        }

        $request->user()->resetTwoFactorCode();

        return redirect()->home();
    }
}
