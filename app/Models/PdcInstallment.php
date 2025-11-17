<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PdcInstallment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['pdc_id', 'chequeno', 'amount', 'due_date', 'status', 'bankName', 'data1', 'data2'];

    public function pdc()
    {
        return $this->belongsTo(Pdc::class, 'pdc_id');
    }

    public function followups()
    {
        return $this->hasMany(PdcFollowup::class);
    }
}
