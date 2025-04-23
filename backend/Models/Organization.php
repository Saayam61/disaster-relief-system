<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $primaryKey = 'org_id';

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }

    public function volunteers()
    {
        return $this->hasMany(Volunteer::class);
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }
}

