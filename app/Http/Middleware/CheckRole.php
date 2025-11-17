<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();

        // Check if user has any of the required roles
        if (!$user->hasAnyRole($roles)) {
            // For unified dashboard, we can be more flexible
            // Allow access if user has appropriate permissions instead of specific role
            $requiredPermissions = $this->getRequiredPermissionsForRoute($request);

            if ($requiredPermissions && $user->hasAnyPermission($requiredPermissions)) {
                return $next($request);
            }

            abort(403, 'Unauthorized access. Insufficient role permissions.');
        }

        return $next($request);
    }

    /**
     * Get required permissions for the current route
     */
    private function getRequiredPermissionsForRoute(Request $request): array
    {
        $routeName = $request->route()->getName();

        $permissionMap = [
            'users.index' => ['manage users'],
            'roles.index' => ['manage roles'],
            'commitments.index' => ['view commitments'],
            'pdcs.index' => ['view pdcs'],
            'clients.index' => ['view clients'],
            // Add more route-permission mappings as needed
        ];

        return $permissionMap[$routeName] ?? [];
    }
}
