<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedInstallment extends Model
{
    protected $fillable = [
        'commitment_id',
        'amount',
        'due_date',
        'status'
    ];
    public function commitment()
    {
        return $this->hasMany(Commitment::class);
    }
}
