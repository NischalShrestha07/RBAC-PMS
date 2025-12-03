<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Department;
use App\Models\Branch;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create sample department and branch
        $department = Department::firstOrCreate([
            'name' => 'Head Office',
            'code' => 'HO',
            'color' => '#3498db',
            'is_active' => true
        ]);

        $branch = Branch::firstOrCreate([
            'name' => 'Main Branch',
            'code' => 'MB001',
            'address' => 'Main Street',
            'is_active' => true
        ]);

        // Essential permissions only - these are the permissions your code expects
        $permissions = [
            // Dashboard
            ['name' => 'view dashboard', 'group' => 'dashboard', 'description' => 'Access to dashboard'],

            // User Management
            ['name' => 'manage users', 'group' => 'user management', 'description' => 'Create, edit, delete users'],
            ['name' => 'manage roles', 'group' => 'user management', 'description' => 'Manage roles and permissions'],

            // Department Management
            ['name' => 'view departments', 'group' => 'departments', 'description' => 'View departments'],
            ['name' => 'manage departments', 'group' => 'departments', 'description' => 'Manage departments'],

            // Commitments
            ['name' => 'view commitments', 'group' => 'commitments', 'description' => 'View commitments'],
            ['name' => 'view all commitments', 'group' => 'commitments', 'description' => 'View all commitments across departments'],
            ['name' => 'view own commitments only', 'group' => 'commitments', 'description' => 'View only own created commitments'],
            ['name' => 'create commitments', 'group' => 'commitments', 'description' => 'Create new commitments'],
            ['name' => 'edit commitments', 'group' => 'commitments', 'description' => 'Edit existing commitments'],
            ['name' => 'delete commitments', 'group' => 'commitments', 'description' => 'Delete commitments'],

            // PDCs
            ['name' => 'view pdcs', 'group' => 'pdcs', 'description' => 'View PDCs'],
            ['name' => 'view all pdcs', 'group' => 'pdcs', 'description' => 'View all PDCs across departments'],
            ['name' => 'view own pdcs only', 'group' => 'pdcs', 'description' => 'View only own created PDCs'],
            ['name' => 'create pdcs', 'group' => 'pdcs', 'description' => 'Create new PDCs'],
            ['name' => 'edit pdcs', 'group' => 'pdcs', 'description' => 'Edit existing PDCs'],
            ['name' => 'delete pdcs', 'group' => 'pdcs', 'description' => 'Delete PDCs'],

            // Clients
            ['name' => 'view clients', 'group' => 'clients', 'description' => 'View clients'],
            ['name' => 'create clients', 'group' => 'clients', 'description' => 'Create new clients'],
            ['name' => 'edit clients', 'group' => 'clients', 'description' => 'Edit existing clients'],
            ['name' => 'delete clients', 'group' => 'clients', 'description' => 'Delete clients'],

            // Agents
            ['name' => 'view agents', 'group' => 'agents', 'description' => 'View agents'],
            ['name' => 'view all agents', 'group' => 'agents', 'description' => 'View all agents across departments'],
            ['name' => 'create agents', 'group' => 'agents', 'description' => 'Create new agents'],
            ['name' => 'edit agents', 'group' => 'agents', 'description' => 'Edit existing agents'],
            ['name' => 'delete agents', 'group' => 'agents', 'description' => 'Delete agents'],
            ['name' => 'view team', 'group' => 'agents', 'description' => 'View team members'],

            // Installments
            ['name' => 'view installments', 'group' => 'installments', 'description' => 'View installments'],
            ['name' => 'create installments', 'group' => 'installments', 'description' => 'Create new installments'],
            ['name' => 'edit installments', 'group' => 'installments', 'description' => 'Edit existing installments'],
            ['name' => 'delete installments', 'group' => 'installments', 'description' => 'Delete installments'],

            // Transactions
            ['name' => 'create transactions', 'group' => 'transactions', 'description' => 'Create transactions'],

            // Financial Reports
            ['name' => 'view financial reports', 'group' => 'reports', 'description' => 'View financial reports'],

            // Cheque Locations
            ['name' => 'view cheque locations', 'group' => 'cheque locations', 'description' => 'View cheque locations'],
            ['name' => 'create cheque locations', 'group' => 'cheque locations', 'description' => 'Create new cheque locations'],
            ['name' => 'edit cheque locations', 'group' => 'cheque locations', 'description' => 'Edit cheque locations'],
            ['name' => 'delete cheque locations', 'group' => 'cheque locations', 'description' => 'Delete cheque locations'],

            // Notifications
            ['name' => 'view notifications', 'group' => 'notifications', 'description' => 'View notifications'],
            ['name' => 'mark notifications read', 'group' => 'notifications', 'description' => 'Mark notifications as read'],

            // Profile
            ['name' => 'view profile', 'group' => 'profile', 'description' => 'View own profile'],
            ['name' => 'edit profile', 'group' => 'profile', 'description' => 'Edit own profile'],
            ['name' => 'change password', 'group' => 'profile', 'description' => 'Change own password'],

            // Followups
            ['name' => 'create followups', 'group' => 'followups', 'description' => 'Create followups'],

            // System
            ['name' => 'manage system settings', 'group' => 'system', 'description' => 'Manage system settings'],
            ['name' => 'access all departments', 'group' => 'system', 'description' => 'Access all departments'],

            // Activity Log permissions
            ['name' => 'view activity logs', 'group' => 'Logs', 'description' => 'View system activity logs'],
            ['name' => 'export activity logs', 'group' => 'Logs', 'description' => 'Export activity logs to CSV'],
            ['name' => 'manage activity logs', 'group' => 'Logs', 'description' => 'Manage and clean up activity logs'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission['name'],
                'guard_name' => 'web'
            ], [
                'group' => $permission['group'],
                'description' => $permission['description']
            ]);
        }

        // Create ONLY Super Admin role - other roles will be created dynamically by users
        $superAdmin = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
            'is_active' => true
        ]);
        $superAdmin->givePermissionTo(Permission::all());

        // Create ONLY Super Admin user - other users/roles will be created through the application
        $superAdminUser = User::firstOrCreate([
            'email' => 'superadmin@gmail.com'
        ], [
            'name' => 'Super Admin',
            'password' => bcrypt('superadmin123'),
            'department_id' => $department->id,
            'branch_id' => $branch->id,
            'is_active' => true
        ]);
        $superAdminUser->assignRole('Super Admin');


        $departments = Department::all();

        foreach ($departments as $department) {
            Permission::firstOrCreate([
                'name' => 'access department ' . $department->id,
                'guard_name' => 'web'
            ], [
                'group' => 'department access',
                'description' => 'Access to department: ' . $department->name
            ]);
        }

        // Add to appropriate roles
        Role::findByName('Super Admin')->givePermissionTo([
            'view activity logs',
            'export activity logs',
            'manage activity logs',
        ]);

        // Role::findByName('Admin')->givePermissionTo([
        //     'view activity logs',
        //     'export activity logs',
        // ]);

        $this->command->info('✅ Department-specific permissions created');
        $this->command->info('✅ Essential permissions created');
        $this->command->info('✅ Super Admin role created with all permissions');
        $this->command->info('✅ Super Admin user created (email: superadmin@gmail.com, password: superadmin123)');
        $this->command->info('📝 Other roles can now be created dynamically through the application');
    }
}
