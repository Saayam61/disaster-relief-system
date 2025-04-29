<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    protected $primaryKey = 'contribution_id';

    protected $fillable = [
        'center_id',
        'org_id',
        'user_id',
        'volunteer_id',
        'name',
        'quantity',
        'unit',
        'type',
        'description',
    ];

    /**
     * Get the relief center that received or donated the contribution.
     */
    public function reliefCenter()
    {
        return $this->belongsTo(ReliefCenter::class, 'center_id');
    }

    /**
     * Get the organization that made the contribution.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }

    /**
     * Get the user who made the contribution.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the volunteer who made the contribution.
     */
    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class, 'volunteer_id');
    }
}
