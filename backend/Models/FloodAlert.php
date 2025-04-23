<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FloodAlert extends Model
{
    protected $primaryKey = 'alert_id';

    public function admin()
    {
        return $this->belongsTo(Users::class, 'admin_id');
    }
}

