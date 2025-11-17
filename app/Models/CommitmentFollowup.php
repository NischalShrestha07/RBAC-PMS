<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitmentFollowup extends Model
{
    use HasFactory;

    protected $fillable = [
        'commitment_id',
        'installment_id',
        'original_due_date',
        'followup_date',
        'renew_date',
        'comments',
        'status',
        'followup_count',
    ];

    protected $casts = [
        'original_due_date' => 'date',
        'followup_date' => 'date',
        'next_due_date'    => 'date',
    ];

    /**
     * Relationship with Commitment.
     */
    public function commitment()
    {
        return $this->belongsTo(Commitment::class);
    }
    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }
}
