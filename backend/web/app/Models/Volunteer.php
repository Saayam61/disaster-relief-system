<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
    protected $primaryKey = 'volunteer_id';

    protected $fillable = [
        'user_id',
        'center_id',
        'org_id',
        'approval_status',
        'skills',
        'availability',
        'status',
    ];

    /**
     * The user associated with this volunteer.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The relief center the volunteer is assigned to.
     */
    public function reliefCenter()
    {
        return $this->belongsTo(ReliefCenter::class, 'center_id');
    }

    /**
     * The organization this volunteer is affiliated with.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }

    /**
     * Contributions made by this volunteer.
     */
    public function contributions()
    {
        return $this->hasMany(Contribution::class, 'user_id', 'user_id');
    }

    public function communications()
    {
        return $this->hasMany(Communication::class, 'sender_id', 'volunteer_id');
    }
}
