<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Installment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'commitment_id',
        'amount',
        'due_date',
        'paid_amount',
        'status',
        'bankName',
        'data1'
    ];

    // Relationship: An installment belongs to a commitment
    public function commitment()
    {
        return $this->belongsTo(Commitment::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transactions::class);
    }

    public function partialPayments()
    {
        return $this->hasMany(PartialPayment::class);
    }
    public function followups()
    {
        return $this->hasMany(CommitmentFollowup::class);
    }
}
