<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Check if user is active
        if (!Auth::user()->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account is deactivated. Please contact administrator.');
        }

        // For unified dashboard, we don't need role-specific redirects
        // Just ensure user has basic dashboard access permission
        if ($request->route()->getName() === 'login' && Auth::check()) {
            return redirect()->route('dashboard.unified')->with('info', 'You are already logged in.');
        }

        return $next($request);
    }
}
