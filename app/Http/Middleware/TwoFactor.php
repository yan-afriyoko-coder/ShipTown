<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactor
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (config('two_factor_auth.disabled')
            || $request->is('verify')
            || $request->user()->two_factor_code === null) {
            return $next($request);
        }

        if ($request->user()->two_factor_expires_at->isPast()) {
            Auth::logout();
        }

        return redirect()->route('verify.index');
    }
}
