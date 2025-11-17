<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckDashboardAccess
{
    /**
     * Handle incoming request for dashboard access
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has dashboard access permission
        if (!$user->can('view dashboard')) {
            abort(403, 'You do not have permission to access the dashboard.');
        }

        // Check if user's role is active
        $primaryRole = $user->getRoleNames()->first();
        if ($primaryRole) {
            $role = \Spatie\Permission\Models\Role::where('name', $primaryRole)->first();
            if ($role && !$role->is_active) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Your role has been deactivated. Please contact administrator.');
            }
        }

        return $next($request);
    }
}
