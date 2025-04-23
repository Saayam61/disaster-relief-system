<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $primaryKey = 'request_id';

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }

    public function reliefCenter()
    {
        return $this->belongsTo(ReliefCenter::class, 'center_id');
    }
}

