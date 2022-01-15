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
        if (! config('two_factor_auth.enabled')) {
            return $next($request);
        }

        if ($request->is('verify')) {
            return $next($request);
        }

        /** @var User $user */
        $user = auth()->user();

        if ($user->two_factor_code) {
            if ($user->two_factor_expires_at->isPast()) {
                Auth::logout();
            }

            return redirect()->route('verify.index');
        }

        return $next($request);
    }
}
