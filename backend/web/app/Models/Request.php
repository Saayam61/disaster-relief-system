<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    // Explicitly setting the primary key
    protected $primaryKey = 'request_id';

    // Explicitly defining the table name if not following convention (optional)
    // protected $table = 'requests'; // Laravel uses the plural 'requests' by default

    // Enabling mass assignment for the fields (this could be adjusted to fit your needs)
    protected $fillable = [
        'user_id', 
        'center_id', 
        'request_type', 
        'status', 
        'description', 
        'quantity', 
        'unit', 
        'urgency'
    ];

    // Defining the relationships with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Defining the relationships with the ReliefCenter model
    public function reliefCenter()
    {
        return $this->belongsTo(ReliefCenter::class, 'center_id');
    }
}
