<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::latest()->paginate(10);
        $notifications = $this->getNotifications();

        return view('permissions.index', compact('permissions', 'notifications'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'group' => 'required|string|max:255',
        ]);

        Permission::create($request->all());

        return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'group' => 'required|string|max:255',
        ]);

        $permission->update($request->all());

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
