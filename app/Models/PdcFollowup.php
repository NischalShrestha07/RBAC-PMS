<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdcFollowup extends Model
{
    use HasFactory;
    protected $table = 'pdc_followups'; // Specify the table name if it's not the default


    protected $fillable = [
        'pdc_id',
        'pdc_installment_id',
        'original_due_date',
        'followup_date',
        'next_due_date',
        'status',
        'followup_count',
    ];

    public function pdc()
    {
        return $this->belongsTo(Pdc::class, 'pdc_id');
    }

    public function installment()
    {
        return $this->belongsTo(PdcInstallment::class);
    }
}
