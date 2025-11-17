<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartialPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'installment_id',
        'payment_amount',
        'remarks'
    ];

    // Relationship with installment
    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }
}
