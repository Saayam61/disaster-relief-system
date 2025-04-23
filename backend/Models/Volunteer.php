<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
    protected $primaryKey = 'volunteer_id';

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }

    public function reliefCenter()
    {
        return $this->belongsTo(ReliefCenter::class, 'center_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }
}

