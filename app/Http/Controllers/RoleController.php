<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        $this->authorize('manage roles');

        $roles = Role::with(['permissions', 'users'])
            ->withCount(['users', 'permissions'])
            ->latest()
            ->paginate(10);

        $permissions = Permission::all()->groupBy('group');
        $permissionGroups = Permission::distinct()->pluck('group');
        $notifications = $this->getNotifications();

        return view('admin.roles.index', compact('roles', 'permissions', 'permissionGroups', 'notifications'));
    }

    public function create()
    {
        $this->authorize('manage roles');

        $permissions = Permission::all()->groupBy('group');
        $notifications = $this->getNotifications();

        return view('admin.roles.create', compact('permissions', 'notifications'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage roles');

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id',
            'is_active' => 'boolean',
            'dashboard_access' => 'required|string|in:full,limited,custom',
        ]);

        DB::transaction(function () use ($request) {
            // Create the role with only basic fields
            $role = Role::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
                'dashboard_access' => $request->dashboard_access,
                'guard_name' => 'web',
            ]);

            // Get permission names from IDs and sync
            $permissionNames = Permission::whereIn('id', $request->permissions)
                ->pluck('name')
                ->toArray();

            // Remove auto-add permissions if not using dashboard_access
            $essentialPermissions = $this->getEssentialPermissions($request->dashboard_access);
            $permissionNames = array_unique(array_merge($permissionNames, $essentialPermissions));

            $role->syncPermissions($permissionNames);
        });

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function show(Role $role)
    {
        $this->authorize('manage roles');

        $role->load(['permissions', 'users' => function ($query) {
            $query->with('department', 'branch');
        }]);

        $permissions = $role->permissions->groupBy('group');
        $notifications = $this->getNotifications();

        return view('admin.roles.show', compact('role', 'permissions', 'notifications'));
    }

    public function edit(Role $role)
    {
        $this->authorize('manage roles');

        // Prevent editing Super Admin role for non-super admins
        if ($role->name === 'Super Admin' && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Cannot edit Super Admin role.');
        }

        $permissions = Permission::all()->groupBy('group');
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        $notifications = $this->getNotifications();

        $recentUsers = $role->users()
            ->latest()
            ->take(5)
            ->get(['name', 'created_at']);

        $topPermissions = Permission::withCount('roles')
            ->orderBy('roles_count', 'desc')
            ->take(5)
            ->get(['name']);


        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions', 'notifications', 'recentUsers', 'topPermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('manage roles');

        // Prevent editing Super Admin role name and critical permissions
        if ($role->name === 'Super Admin' && !auth()->user()->hasRole('Super Admin')) {
            return redirect()->route('roles.index')->with('error', 'Cannot modify Super Admin role.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id',
            'is_active' => 'boolean',
            'dashboard_access' => 'required|string|in:full,limited,custom',
        ]);

        DB::transaction(function () use ($request, $role) {
            $role->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->is_active ?? $role->is_active,
                'dashboard_access' => $request->dashboard_access,
            ]);

            // Get permission names from IDs and sync
            $permissionNames = Permission::whereIn('id', $request->permissions)
                ->pluck('name')
                ->toArray();

            // Remove auto-add permissions if not using dashboard_access
            $essentialPermissions = $this->getEssentialPermissions($request->dashboard_access);
            $permissionNames = array_unique(array_merge($permissionNames, $essentialPermissions));

            // Prevent Super Admin from losing critical permissions
            if ($role->name === 'Super Admin') {
                $criticalPermissions = [
                    'manage users',
                    'manage roles',
                    'manage system settings',
                    'view dashboard'
                ];
                $permissionNames = array_unique(array_merge($permissionNames, $criticalPermissions));
            }

            $role->syncPermissions($permissionNames);
        });

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $this->authorize('manage roles');

        // Prevent deletion of super admin role and roles with users
        if ($role->name === 'Super Admin') {
            return redirect()->route('roles.index')->with('error', 'Cannot delete Super Admin role.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', 'Cannot delete role that has assigned users.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }

    public function toggleStatus(Role $role)
    {
        $this->authorize('manage roles');

        if ($role->name === 'Super Admin') {
            return redirect()->back()->with('error', 'Cannot deactivate Super Admin role.');
        }

        // Only update if is_active field exists
        if (Schema::hasColumn('roles', 'is_active')) {
            $role->update([
                'is_active' => !$role->is_active
            ]);

            $status = $role->is_active ? 'activated' : 'deactivated';
            return redirect()->back()->with('success', "Role {$status} successfully.");
        }

        return redirect()->back()->with('error', 'Role status feature not available.');
    }

    public function duplicate(Role $role)
    {
        $this->authorize('manage roles');

        DB::transaction(function () use ($role) {
            $newRole = $role->replicate();
            $newRole->name = $role->name . ' - Copy';
            $newRole->created_at = now();
            $newRole->updated_at = now();
            $newRole->save();

            // Copy permissions using names
            $permissionNames = $role->permissions->pluck('name')->toArray();
            $newRole->syncPermissions($permissionNames);
        });

        return redirect()->route('roles.index')
            ->with('success', 'Role duplicated successfully.');
    }

    /**
     * Remove this method if not using dashboard_access
     */
    private function getEssentialPermissions($dashboardAccess)
    {
        $essentialPermissions = ['view dashboard'];

        switch ($dashboardAccess) {
            case 'full':
                $essentialPermissions = array_merge($essentialPermissions, [
                    'view dashboard',
                    'manage users',
                    'manage roles',
                    'view reports'
                ]);
                break;
            case 'limited':
                $essentialPermissions = array_merge($essentialPermissions, [
                    'view dashboard',
                    'view commitments',
                    'view pdcs',
                    'view clients'
                ]);
                break;
            case 'custom':
                // Only basic dashboard view
                $essentialPermissions = ['view dashboard'];
                break;
        }

        return $essentialPermissions;
    }
}
