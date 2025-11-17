<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{

    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'department_id',
        'branch_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function agents()
    {
        return $this->hasMany(Agent::class, 'created_by');
    }



    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationship with Department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Relationship with Branch
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Check if user can access module
    public function canAccessModule($module)
    {
        if ($this->hasRole('Super Admin')) {
            return true;
        }

        $modulePermissions = [
            'dashboard' => 'view dashboard',
            'commitments' => 'view commitments',
            'pdcs' => 'view pdcs',
            'agents' => 'view agents',
            'clients' => 'view clients',
            'installments' => 'view installments',
            'transactions' => 'view transactions',
            'reports' => 'view reports',
            'cheque_locations' => 'view cheque locations',
            'users' => 'manage users',
            'departments' => 'manage departments',
            'roles' => 'manage roles',
        ];

        return isset($modulePermissions[$module]) && $this->can($modulePermissions[$module]);
    }

    // Check department access
    public function hasDepartmentAccess($departmentId)
    {
        if ($this->hasRole('Super Admin')) {
            return true;
        }

        return $this->department_id == $departmentId;
    }

    // Check branch access
    public function hasBranchAccess($branchId)
    {
        if ($this->hasRole('Super Admin')) {
            return true;
        }

        return $this->branch_id == $branchId;
    }
}
