<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Communication extends Model
{
    protected $primaryKey = 'message_id';

    public function sender()
    {
        return $this->belongsTo(Users::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Users::class, 'receiver_id');
    }
}

