<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commitment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'total_amount', 'department', 'department_id', 'paid_amount', 'pending_amount', 'due_date', 'status', 'remarks', 'client_id', 'agent_id'];


    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
    public function pdcs()
    {
        return $this->hasMany(Pdc::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }




    public function calculatePendingAmount()
    {
        return $this->total_amount - $this->paid_amount;
    }
    public function updatePendingAmount()
    {
        $this->pending_amount = $this->calculatePendingAmount();
        $this->save();
    }

    /**
     * Update the status of the commitment based on the paid amount.
     */
    public function updateStatus()
    {
        if ($this->paid_amount >= $this->total_amount) {
            $this->status = 'completed';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'pending';
        } else {
            $this->status = 'exceeded';
        }
        $this->save();
    }

    public function followups()
    {
        return $this->hasMany(CommitmentFollowup::class);
    }

    public function allFollowups()
    {
        return $this->followups()->with('installment');
    }

    // Relationship: A commitment has many installments
    public function installments()
    {
        return $this->hasMany(Installment::class, 'commitment_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transactions::class, 'pc_id');
    }

    public function failedInstallments()
    {
        return $this->hasMany(FailedInstallment::class);
    }
    public function activeInstallments()
    {
        return $this->hasMany(Installment::class)->whereNotIn('status', ['Failed']);
    }
}
