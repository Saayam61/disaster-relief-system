<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Communication extends Model
{
    protected $primaryKey = 'message_id';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'timestamp',
        'read_status',
    ];

    public function sender()
    {
        return $this->belongsTo(Users::class, 'sender_id', 'user_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Users::class, 'receiver_id', 'user_id');
    }
}

