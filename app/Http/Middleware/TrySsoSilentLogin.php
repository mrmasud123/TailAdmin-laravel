<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrySsoSilentLogin
{
    public function handle(Request $request, Closure $next)
    {
        // Already logged in — nothing to do
        if (Auth::check()) {
            return $next($request);
        }

        // Already checked once this round trip — don't loop, just show guest page
        if ($request->query('sso') === 'none' || $request->query('token')) {
            return $next($request);
        }

        // Never checked yet — bounce through Hospital's silent-check
        return redirect()->away('https://hospital.test/sso/silent-check');
    }
}
