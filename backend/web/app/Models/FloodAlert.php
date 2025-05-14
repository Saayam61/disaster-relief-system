<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FloodAlert extends Model
{
    protected $table = 'flood_alerts';
    protected $primaryKey = 'alert_id';

    // Enabling mass assignment for the fields (this could be adjusted to fit your needs)
    protected $fillable = [
        'admin_id',
        'message',
        'severity',
        'description',
        'is_active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
