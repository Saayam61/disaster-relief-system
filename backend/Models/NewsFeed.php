<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsFeed extends Model
{
    protected $primaryKey = 'post_id';

    public function reliefCenter()
    {
        return $this->belongsTo(ReliefCenter::class, 'center_id');
    }
}

