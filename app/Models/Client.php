<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = ['name', 'email', 'address', 'phone', 'company', 'department'];

    public function pdcs()
    {
        return $this->hasMany(Pdc::class);
    }

    public function pdcHistory()
    {
        return $this->hasMany(Pdc::class, 'client_id');
    }
    public function commitments()
    {
        return $this->hasMany(Commitment::class, 'client_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
