<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $primaryKey = 'org_id';

    protected $fillable = [
        'user_id',
        'org_name',
        'type',
        'volunteers_sent',
        'is_verified',
    ];

    /**
     * The user who registered or represents this organization.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Volunteers associated with this organization.
     */
    public function volunteers()
    {
        return $this->hasMany(Volunteer::class, 'org_id');
    }

    /**
     * Contributions made by this organization.
     */
    public function contributions()
    {
        return $this->hasMany(Contribution::class, 'org_id');
    }
}
