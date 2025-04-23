<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Users extends Authenticatable
{
    use Notifiable;
    protected $primaryKey = 'user_id'; // Custom primary key

    public function floodAlerts()
    {
        return $this->hasMany(FloodAlert::class, 'admin_id');
    }

    public function reliefCenter()
    {
        return $this->hasOne(ReliefCenter::class);
    }

    public function organizations()
    {
        return $this->hasOne(Organization::class);
    }

    public function volunteer()
    {
        return $this->hasOne(Volunteer::class);
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    public function communications()
    {
        return $this->hasMany(Communication::class, 'sender_id');
    }
}

