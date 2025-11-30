<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->can($permission)) {
            abort(403, "Unauthorized action. You don't have permission to access this resource.");
        }

        return $next($request);
    }
}
