<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use Notifiable;
    use HasApiTokens;

    protected $primaryKey = 'user_id'; // Custom primary key

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'password',
        'role',
        'latitude',
        'longitude',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function floodAlerts()
    {
        return $this->hasMany(FloodAlert::class, 'admin_id');
    }

    public function reliefCenter()
    {
        return $this->hasOne(ReliefCenter::class, 'user_id', 'user_id');
    }

    public function organizations()
    {
        return $this->hasOne(Organization::class, 'user_id');
    }

    public function volunteer()
    {
        return $this->hasOne(Volunteer::class, 'user_id');
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class, 'user_id', 'user_id');
    }

    public function communications()
    {
        return $this->hasMany(Communication::class, 'sender_id', 'user_id');
    }

    public function requests()
    {
        return $this->hasMany(Request::class, 'user_id', 'user_id');
    }

    public function canPostNews()
    {
        return in_array($this->role, ['Relief Center']);
    }

    public function notifications()
    {
        return $this->morphMany(\Illuminate\Notifications\DatabaseNotification::class, 'notifiable', 'notifiable_type', 'notifiable_id', 'user_id');
    }

    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

}
