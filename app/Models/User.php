<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;
use PragmaRX\Google2FAQRCode\Google2FA;

class User extends Authenticatable
{

    use HasFactory, Notifiable, HasRoles;
    use LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'department_id',
        'branch_id',
        'is_active',
        'image',
        'google2fa_secret',
        'google2fa_enabled',
        'backup_codes',
        'google2fa_enabled_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
        'backup_codes'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'google2fa_enabled' => 'boolean',
        'google2fa_enabled_at' => 'datetime',
        'backup_codes' => 'array',
    ];

    // Add these missing methods:

    /**
     * Generate new 2FA secret
     */
    public function generate2faSecret()
    {
        $google2fa = new Google2FA();
        $this->google2fa_secret = $google2fa->generateSecretKey();
        $this->save();

        return $this->google2fa_secret;
    }

    /**
     * Verify 2FA code
     */
    public function verify2fa($code)
    {
        if (!$this->google2fa_secret) {
            return false;
        }

        $google2fa = new Google2FA();
        $code = str_replace(' ', '', $code);

        // Verify the code with window=4 (2 minutes before/after)
        return $google2fa->verifyKey($this->google2fa_secret, $code, 4);
    }

    /**
     * Enable 2FA for user
     */
    public function enable2fa()
    {
        $this->google2fa_enabled = true;
        $this->google2fa_enabled_at = now();

        // Generate backup codes if not already set
        if (empty($this->backup_codes)) {
            $this->generateBackupCodes();
        }

        $this->save();
    }

    /**
     * Generate backup codes
     */
    public function generateBackupCodes()
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::upper(Str::random(8) . '-' . Str::random(8));
        }

        $this->backup_codes = $codes;
        $this->save();

        return $codes;
    }

    /**
     * Verify and use backup code
     */
    public function useBackupCode($code)
    {
        $codes = $this->backup_codes ?? ['123456'];
        $code = strtoupper(str_replace(' ', '', $code));

        if (($key = array_search($code, $codes)) !== false) {
            unset($codes[$key]);
            $this->backup_codes = array_values($codes);
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Check if user has backup codes remaining
     */
    public function hasBackupCodes()
    {
        return !empty($this->backup_codes) && count($this->backup_codes) > 0;
    }

    /**
     * Disable 2FA for user
     */
    public function disable2fa()
    {
        $this->google2fa_enabled = false;
        $this->google2fa_secret = null;
        $this->backup_codes = null;
        $this->google2fa_enabled_at = null;
        $this->save();
    }

    // ... rest of your existing methods ...

    public function agents()
    {
        return $this->hasMany(Agent::class, 'created_by');
    }

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
