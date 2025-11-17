<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckDepartmentAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Super Admin has access to everything
        if ($user->hasRole('Super Admin')) {
            return $next($request);
        }

        // Users with 'access all departments' permission can access everything
        if ($user->hasPermissionTo('access all departments')) {
            return $next($request);
        }

        // Check department access from various sources and extract ID if needed
        $departmentId = $this->extractDepartmentId($request);

        if ($departmentId) {
            // If user has specific department access permission
            if ($user->hasPermissionTo('access department ' . $departmentId)) {
                return $next($request);
            }

            // If user belongs to the department
            if ($user->department_id == $departmentId) {
                return $next($request);
            }

            abort(403, 'You do not have access to this department.');
        }

        return $next($request);
    }

    /**
     * Extract department ID from request, handling both ID and object inputs
     */
    private function extractDepartmentId(Request $request): ?int
    {
        $departmentParam = $request->route('department') ??
            $request->department_id ??
            $request->input('department_id');

        if (!$departmentParam) {
            return null;
        }

        // If it's already an integer, return it
        if (is_numeric($departmentParam)) {
            return (int) $departmentParam;
        }

        // If it's an array or object with an ID property
        if (is_array($departmentParam) && isset($departmentParam['id'])) {
            return (int) $departmentParam['id'];
        }

        if (is_object($departmentParam) && isset($departmentParam->id)) {
            return (int) $departmentParam->id;
        }

        // If it's a JSON string, try to decode it
        if (is_string($departmentParam) && str_contains($departmentParam, '{')) {
            $decoded = json_decode($departmentParam, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['id'])) {
                return (int) $decoded['id'];
            }
        }

        // If we can't extract an ID, return null
        return null;
    }
}
