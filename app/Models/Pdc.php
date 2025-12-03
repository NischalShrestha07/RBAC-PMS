<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pdc extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'commitment_id',
        'status',
        'name',
        'commitment_amount',
        'cheque_number',
        'bank_name',
        'cheque_date',
        'created_by',
        'remarks',
        'department',
        'department_id',
        'client_id',
        'agent_id',
    ];

    public function transactions()
    {
        return $this->hasMany(Transactions::class, 'pc_id')->where('type', 'pdc');
    }

    public function installments()
    {
        return $this->hasMany(PdcInstallment::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function commitment()
    {
        // return $this->belongsTo(Commitment::class, 'commitment_id');
        return $this->belongsTo(Commitment::class);
    }


    // Relation with User
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function followups()
    {
        return $this->hasMany(PdcFollowup::class, 'pdc_id');
    }

    public function failedInstallments()
    {
        return $this->hasMany(FailedPdcInstallment::class);
    }

    public function activeInstallments()
    {
        return $this->hasMany(PdcInstallment::class)->whereNotIn('status', ['Failed']);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
