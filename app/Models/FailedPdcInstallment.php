<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedPdcInstallment extends Model
{
    protected $fillable = ['pdc_id', 'amount', 'chequeno', 'bankName', 'due_date', 'status'];

    public function pdc()
    {
        return $this->belongsTo(Pdc::class);
    }
}
