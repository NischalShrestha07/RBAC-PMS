<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChequeMenu extends Model
{
    protected $table = 'cheque_menus';
    protected $fillable = ['client_id', 'cheque_no', 'amount', 'agent_id', 'location', 'remarks', 'details'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
