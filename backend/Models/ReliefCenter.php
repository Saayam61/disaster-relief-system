<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReliefCenter extends Model
{
    protected $primaryKey = 'center_id';

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }

    public function volunteers()
    {
        return $this->hasMany(Volunteer::class);
    }

    public function newsFeed()
    {
        return $this->hasMany(NewsFeed::class);
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }
}

