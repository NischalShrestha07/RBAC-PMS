<?php
// app/Http/Controllers/DepartmentController.php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::query();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->has('status') && in_array($request->status, ['active', 'inactive'])) {
            $query->where('is_active', $request->status === 'active');
        }

        $departments = $query->withCount('users')->latest()->paginate(10);
        $notifications = $this->getNotifications();

        return view('departments.index', compact('departments', 'notifications'));
    }

    public function create()
    {
        $notifications = $this->getNotifications();

        return view('departments.create', compact('notifications'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'code' => 'required|string|max:50|unique:departments,code',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $department = Department::create([
                    'name' => $request->name,
                    'code' => Str::upper($request->code),
                    'description' => $request->description,
                    'color' => $request->color ?? '#3498db',
                    'is_active' => $request->boolean('is_active'),
                ]);
            });

            return redirect()->route('departments.index')
                ->with('success', 'Department created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating department: ' . $e->getMessage());
        }
    }

    public function show(Department $department)
    {
        $department->load(['users' => function ($query) {
            $query->with(['roles', 'branch'])->latest();
        }]);
        $notifications = $this->getNotifications();


        return view('departments.show', compact('department', 'notifications'));
    }

    public function edit(Department $department)
    {
        $notifications = $this->getNotifications();

        return view('departments.edit', compact('department', 'notifications'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'code' => 'required|string|max:50|unique:departments,code,' . $department->id,
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        try {
            $department->update([
                'name' => $request->name,
                'code' => Str::upper($request->code),
                'description' => $request->description,
                'color' => $request->color,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return redirect()->route('departments.index')
                ->with('success', 'Department updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating department: ' . $e->getMessage());
        }
    }

    public function destroy(Department $department)
    {
        // Check if department has users
        if ($department->users()->count() > 0) {
            return redirect()->route('departments.index')
                ->with('error', 'Cannot delete department that has assigned users. Please reassign users first.');
        }

        try {
            $department->delete();



            return redirect()->route('departments.index')
                ->with('success', 'Department deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('departments.index')
                ->with('error', 'Error deleting department: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Department $department)
    {
        $department->update([
            'is_active' => !$department->is_active
        ]);

        $status = $department->is_active ? 'activated' : 'deactivated';


        return redirect()->back()->with('success', "Department {$status} successfully.");
    }
}
