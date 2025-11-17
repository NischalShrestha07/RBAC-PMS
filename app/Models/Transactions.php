<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

    protected $fillable = [
        'pc_id',
        'type',
        'installment_id',
        'client_id',
        'agent_id',
        'pay_amount',
        'login_uid',
        'remarks'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'login_uid');
    }

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }
    // public function pdc_installment()
    // {
    //     return $this->belongsTo(PdcInstallment::class);
    // }
}
