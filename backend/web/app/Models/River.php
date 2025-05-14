<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class River extends Model
{
    protected $table = 'rivers';
    protected $primaryKey = 'id';

    // Enabling mass assignment for the fields (this could be adjusted to fit your needs)
    protected $fillable = [
        'river_id', 
        'latitude', 
        'longitude'
    ];
}
