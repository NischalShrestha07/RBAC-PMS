<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only process for authenticated users
        if (Auth::check()) {
            $user = Auth::user();
            $routeName = $request->route()->getName();

            // Redirect away from login/register pages if already authenticated
            if (in_array($routeName, ['login', 'register']) && $user->is_active) {
                return redirect()->route('dashboard.unified');
            }

            // Check if user is trying to access dashboard without proper permission
            if (str_contains($routeName, 'dashboard') && !$user->can('view dashboard')) {
                abort(403, 'You do not have permission to access the dashboard.');
            }

            // For non-dashboard routes, ensure user has proper permissions
            if (!$this->hasRouteAccess($user, $routeName)) {
                abort(403, 'You do not have permission to access this page.');
            }
        }

        return $next($request);
    }

    /**
     * Check if user has access to the current route
     */
    private function hasRouteAccess($user, $routeName): bool
    {
        // Public routes that don't require specific permissions
        $publicRoutes = [
            'dashboard.unified',
            'logout',
            'view.profile',
            'profile.update',
            'change-password',
        ];

        if (in_array($routeName, $publicRoutes)) {
            return true;
        }

        // Route to permission mapping
        $routePermissionMap = [
            // User Management
            'users.index' => 'manage users',
            'users.create' => 'manage users',
            'users.store' => 'manage users',
            'users.show' => 'manage users',
            'users.edit' => 'manage users',
            'users.update' => 'manage users',
            'users.destroy' => 'manage users',
            'users.toggle-status' => 'manage users',
            'users.permissions' => 'manage users',

            // Role Management
            'roles.index' => 'manage roles',
            'roles.create' => 'manage roles',
            'roles.store' => 'manage roles',
            'roles.show' => 'manage roles',
            'roles.edit' => 'manage roles',
            'roles.update' => 'manage roles',
            'roles.destroy' => 'manage roles',
            'roles.toggle-status' => 'manage roles',
            'roles.duplicate' => 'manage roles',

            // Commitment Management
            'commitments.index' => 'view commitments',
            'commitments.create' => 'create commitments',
            'commitments.store' => 'create commitments',
            'commitments.show' => 'view commitments',
            'commitments.edit' => 'edit commitments',
            'commitments.update' => 'edit commitments',
            'commitments.destroy' => 'delete commitments',
            'commitments.fail' => 'edit commitments',

            // PDC Management
            'pdcs.index' => 'view pdcs',
            'pdcs.create' => 'create pdcs',
            'pdcs.store' => 'create pdcs',
            'pdcs.show' => 'view pdcs',
            'pdcs.edit' => 'edit pdcs',
            'pdcs.update' => 'edit pdcs',
            'pdcs.destroy' => 'delete pdcs',
            'pdcs.fail' => 'edit pdcs',

            // Client Management
            'clients.index' => 'view clients',
            'clients.create' => 'create clients',
            'clients.store' => 'create clients',
            'clients.show' => 'view clients',
            'clients.edit' => 'edit clients',
            'clients.update' => 'edit clients',
            'clients.destroy' => 'delete clients',

            // Department Management
            'departments.index' => 'view departments',
            'departments.create' => 'manage departments',
            'departments.store' => 'manage departments',
            'departments.show' => 'view departments',
            'departments.edit' => 'manage departments',
            'departments.update' => 'manage departments',
            'departments.destroy' => 'manage departments',
            'departments.toggle-status' => 'manage departments',

            // Add more route-permission mappings as needed
        ];

        $requiredPermission = $routePermissionMap[$routeName] ?? null;

        // If no specific permission required, allow access
        if (!$requiredPermission) {
            return true;
        }

        // Check if user has the required permission
        return $user->can($requiredPermission);
    }
}
