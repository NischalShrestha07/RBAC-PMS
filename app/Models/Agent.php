<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model
{
    use SoftDeletes;  // Enable soft deletes
    use LogsActivity;

    protected $dates = ['deleted_at'];  // Ensure deleted_at is treated as a date

    protected $fillable = ['name', 'contact', 'department', 'created_by', 'address', 'area', 'department_id', 'created_by', 'email'];

    public function commitments()
    {
        return $this->hasMany(Commitment::class);
    }
    public function pdcs()
    {
        return $this->hasMany(Pdc::class,);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function postponedCommitments()
    {
        return $this->commitments()->where('status', 'postponed');
    }

    public function successCommitments()
    {
        return $this->commitments()->where('status', 'success');
    }

    public function postponedPDCs()
    {
        return $this->pdcs()->where('status', 'postponed');
    }

    public function successPDCs()
    {
        return $this->pdcs()->where('status', 'success');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
