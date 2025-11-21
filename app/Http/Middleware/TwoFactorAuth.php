<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->google2fa_enabled && !$request->session()->get('2fa_verified')) {
            if (!$request->is('2fa*') && !$request->is('logout')) {
                $request->session()->put('2fa_intended_url', $request->url());
            }

            if (!$request->is('2fa*')) {
                return redirect()->route('2fa.verify');
            }
        }
        return $next($request);
    }
}
