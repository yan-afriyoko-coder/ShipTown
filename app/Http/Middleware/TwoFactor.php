<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TwoFactor
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (config('two_factor_auth.disabled')) {
            return $next($request);
        }

        if ($request->is('verify')) {
            return $next($request);
        }

        if ($request->cookie('device_guid') !== null) {
            return $next($request);
        }

        return redirect()->route('verify.index');
    }
}
